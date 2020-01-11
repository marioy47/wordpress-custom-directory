import filterItems from './filterItems';

(d => {
	d.querySelectorAll('.custom-directory-search').forEach(form => {

		// Save all the children of the target <ul> in an array.
		const directoryItems = [];
		d.getElementById(form.dataset.target).querySelectorAll('.directory-item').forEach(item => {
			const searchFields = {};
			item.querySelectorAll('.search-item').forEach(span => {
				searchFields[span.dataset.field] = span.innerHTML;
			});
			item['searchFields'] = searchFields;
			directoryItems.push(item);
		});

		// If there is no list, then exit.
		if (0 === directoryItems.length) return;

		// Create an object with all the values of the form
		const formValues = {}
		form.querySelectorAll('input, select').forEach(element => {
			formValues[element.name] = ''; // Create an object with empty values.

			if ('text' === element.type) {
				// Listen for keytypes and execute a search if user types.
				element.addEventListener('keyup', ev => {
					formValues[ev.target.name] = ev.target.value;
					filterItems(directoryItems, formValues);
				}); // keyup
			}

		}); // form[input,select]

	});


})(document);
