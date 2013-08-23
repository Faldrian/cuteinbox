$(function() {

	// Sync-Methode fürs Backbone
	Backbone.sync = function(method, model, options) {
	
	    var params = _.extend({
	        type:        'POST',
	        dataType:    'json',
	        url:         'ajax.php',
	        contentType: 'application/x-www-form-urlencoded;charset=UTF-8'
	    }, options);
	    
	    if (method == 'read') {
	        params.type = 'GET';
	        if('id' in model) {
	        	params.data = model.id;
	        }
	    }
	
	    if (!params.data && model && (method == 'create' || method == 'update' || method == 'delete')) {
	        params.data = JSON.stringify(model.toJSON());
	    }
	
	    if (params.type !== 'GET') {
	        params.processData = false;
	    }
	
	    params.data = jQuery.param({
	    	action: 'sync',
	    	backbone_method:method,
	    	backbone_model:model.dbModel,
	    	content:params.data
	    });
	
	    // Make the request.
	    return jQuery.ajax(params);
	};
	
	
	// *** Namespace für die App
	window.App = {
		Models: {},
		Views: {}
	}


	// *** Models
	App.Models.Post = Backbone.Model.extend({
		dbModel: 'Post'
	});

	App.Models.Posts = Backbone.Collection.extend({
		model: App.Models.Post,
		dbModel: 'Post', // Modelnamen, die enthalten sind.
	});
	
	// Model-Initialisierung
	App.posts = new App.Models.Posts(); // Neues globales Model für die Feeds.
	
	
	// *** Views
	App.Views.PostlistItem = Backbone.View.extend({
		tagName: 'tr',
		
		template: _.template( jQuery('#tpl_PostlistItem').html() ),
		postTPL: _.template( jQuery('#tpl_DiasporaPost').html() ),
		
		events: {
			'change textarea': 'update',
			'click input': 'update'
		},
		
		initialize: function() {
			this.listenTo(this.model, 'change', this.render);
			this.listenTo(this.model, 'destroy', this.remove);
		},
		
		render: function() 	{
			this.$el.html( this.template( this.model.toJSON() ) );
			
			return this;
		},
		
		update: function() {
			var comment = this.$('textarea').val();
			var checked = this.$('input').prop('checked');
			
			// Kommentar schonmal setzen, das muss sowieso
			this.model.set('comment', comment);
			
			// Wenn der Beitrag aktiviert wurde, auch den Body schreiben - sonst löschen
			if(checked) {
				this.model.set('body', this.postTPL( this.model.toJSON() ) );
			} else {
				this.model.set('body', null);
			}
			
			this.model.save();
		}
		
	});
	
	App.Views.Postlist = Backbone.View.extend({
		el: '#Postlist',

		initialize: function() {
			this.listenTo(App.posts, 'add', this.addOne);
			this.listenTo(App.posts, 'reset', this.addAll);
			
			//this.listenTo(App.posts, 'all', this.render);
		},
		
		addOne: function (model) {
			var view = new App.Views.PostlistItem({ model: model });
			this.$el.append( view.render().el );
		},
		
		addAll: function (model) {
			this.$el.html('');
			App.posts.each(this.addOne, this);
		}
	});
	
	
	// URLs hinzufügen
	App.insertmulti_progress = function() {
		jQuery.getJSON('ajax.php', {action: 'insertmulti_progress'}, function(data) {
			// Model updaten
			model = App.posts.get(data.entry.id);
			if(model != undefined) {
				model.set(data.entry);
			} else {
				App.posts.add(data.entry);
			}
			
			// Ruf die Funktion erneut auf, wenn wir noch nicht fertig sind.
			if(data.num_todo > 0) {
				App.insertmulti_progress();
			}
		});
	}
	
	
	$('#addurls button').click(function() {
		var urls = $('#addurls textarea').val();
		
		jQuery.post('ajax.php', {action: 'insertmulti', urls: urls}, function(response) {
				if(response != "0") {
					App.insertmulti_progress();
				}
			});
		
		// Leer das mal wieder, nicht, dass man zuviel doppelt einfügen möchte.
		$('#addurls textarea').val('');
	});
	
	
	
	// Start!
	new App.Views.Postlist(); // Root-Liste initialisieren
	App.posts.fetch(); // Daten holen

});
