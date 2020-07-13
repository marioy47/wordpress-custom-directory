import filterItems from './filterItems';
import debounce from './debounce';
(d => {
	d.querySelectorAll('.custom-directory-search').forEach(form => {
		// We get the list ID from the dataset.target. then we extract all of the items of the list into an array.
		const directoryItems = [];
		d.getElementById(form.dataset.target)
			.querySelectorAll('.directory-item')
			.forEach(listItem => {
				const searchFields = {};
				listItem.querySelectorAll('.search-item').forEach(span => {
					searchFields[span.dataset.field] = span.innerHTML;
				});
				listItem['searchFields'] = searchFields;
				directoryItems.push(listItem);
			});

		// If there is no list, then exit.
		if (0 === directoryItems.length) return;

		// Create an object with all the values of the form
		const formValues = {};
		form.querySelectorAll('input, select').forEach(element => {
			console.log(element.type);
			formValues[element.name] = ''; // Create an object with empty values.

			if ('text' === element.type || 'password' === element.type) {
				// Listen for keytypes and execute a search if user types.
				element.addEventListener(
					'keyup',
					debounce(ev => {
						formValues[ev.target.name] = ev.target.value;
						filterItems(directoryItems, formValues);
					}, 300)
				); // keyup
			}
			if ('select-one' === element.type) {
				element.addEventListener('change', ev => {
					console.log(ev.target.name);
					formValues[ev.target.name] = ev.target.value;
					filterItems(directoryItems, formValues);
				});
			}
		}); // form[input,select]
	});
})(document);
