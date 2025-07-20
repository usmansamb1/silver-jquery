jQuery(function () {
	log("page initilize runner...");
	setTimeout(() => initilizeSearchbar(), 500);

});
function log(e) {
	console.log(e);
}


function initilizeSearchbar() {

	// selector 1st events
	jQuery("#ofs-search-input").select2({
		placeholder: "Type and Select one."
		, allowClear: true
		, closeOnSelect: true,
		minimumInputLength: 2,
		containerCssClass: "ofsSearchClass",
		ajax: {
			url: '/ofs/ofs-search-list',
			data: function (params) {
				return {
					q: params.term// search termss
				};
			},
			processResults: function (data) {
				log("vandor data loaded...");
				return {
					results: data.items
				}
			},
		}
	});

	jQuery("#ofs-search-input")
		.on("change", function (e) { log("change () is in run"); })
		.on("select2:opening", function () { log("opening"); })
		.on("select2:open", function () { log("open"); })
		.on("select2:selecting", function (event) {
			log("selecting  all values=", JSON.stringify(event.items));
			//JsLoadingOverlay.show();
			//jQuery("#ofsApplicationFromSearchResult").submit();
		}).on("select2:select", function (event) {
			var value = $(event.currentTarget).find("option:selected").val();
			console.log(value);
			JsLoadingOverlay.show();
			jQuery("#ofsApplicationFromSearchResult").submit();
		})
		.on("select2:close", function () { log("close"); });


	if (jQuery('#ofs-search-input').hasClass("select2-hidden-accessible")) {
		// Select2 has been initialized
		searchBarHide();

		// attach function to search button
		jQuery("#ofssearchbtn").one("click", handler1);
	}

	//code from partial metaviewport

	jQuery("#ofssearchbtn").click(function () {
		var counter = 0;

		//searchBarShow();
		
		jQuery(this).toggleClass('ofssearchbtn-selected');

		jQuery("#ofs-search-input").val("");
		jQuery("#ofs-search-input").focus();
		jQuery(this).find('i').toggle();




	});


	// auto hide when click outside from a

	$(document).mouseup(function (e) {
		var container = $("#ofs-search-input");
		var container2 = $("#ofssearchbtn");

		if (!container.is(e.target) && container.has(e.target).length == 0
			&& !container2.is(e.target) && container2.has(e.target).length == 0 && (container.css('display') == "block" || container.css('display') == "inline-block") && (container2.css('display') == "inline-block" || container2.css('display') == "block")
		) {
			//container.hide();
			$("#ofs-search-input").toggle();
			//$("#ofssearchbtn").find('i').toggle();
			//searchBarHide();
			//handler2();
			// console.log("closing from clicking outside..");
		}
	});



}

function searchBarHide() {
	jQuery("#ofs-search-input").next().hide();

	console.log("search bar hiding from here (dash)..");
}

function searchBarShow() {
	jQuery("#ofs-search-input").next().show();
	console.log("search bar Showing.. from here (dash)..");
}

function handler1() {
	searchBarShow();
	//alert('First handler: ' + $(this).text());
	$(this).one("click", handler2);
}
function handler2() {
	searchBarHide();
	//alert('Second handler: ' + $(this).text());
	$(this).one("click", handler1);
}
