define([ 'jquery', 'core/ajax' ], function($, ajax) {
	var run = false;

	function update_pages() {

		var url = $('select[name=url]').val();

		var promises = ajax.call([ {
			methodname : 'mod_loop_get_structure',
			args : {
				url : url
			}
		} ]);

		promises[0].done(
				function(response) {
					

					var pages_list = JSON.parse(response.structure);
					var pages_list_parsed = JSON.parse(pages_list);

					pages_select = $('select[name=chapter]');
					pages_select.empty();

					$.each(pages_list_parsed, function(i, chapter) {
						var option = '<option value="' + chapter.key + '">'
								+ chapter.value + '</option>';

						pages_select.append(option);
					});

				}).fail(function(ex) {
			console.log(ex);
		});

	}

	
	function update_themes() {
		
		var url = $('select[name=url]').val();
		
		//console.log(url);
		
		var promises = ajax.call([ {
			methodname : 'mod_loop_get_themes',
			args : {
				url : url
			}
		} ]);

		promises[0].done(
				function(response) {
					

					
					
					
					var themes_list = JSON.parse(response.themes);
					var themes_list_parsed = JSON.parse(themes_list);

					themes_select = $('select[name=theme]');
					themes_select.empty();

					$.each(themes_list_parsed, function(i, theme) {
						var option = '<option value="' + theme.key + '">'
								+ theme.value + '</option>';

						themes_select.append(option);
					});
					
					
					

				}).fail(function(ex) {
			console.log(ex);
		});		
		
	}
	
	
	return {
		init : function() {

			if (run) {
				return;
			}
			run = true;

			$('select[name=url]').change(function() {
				update_pages();
				update_themes();
			});
		}
	};
});