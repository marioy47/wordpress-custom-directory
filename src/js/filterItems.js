
const filterListItems = (listItems, formValues) => {

	// Loop trough each list item searching matches
	listItems.forEach(item => {
		item.style.display = 'initial';
		let showItem = true;

		// Loop for each form item looking for matches in the item.
		for (const key in formValues) {
			if ('undefined' == item.searchFields[key]) continue;
			const searchFor = new RegExp(formValues[key].toLowerCase().trim().replace(/\s+/, ".+"), 'i');
			const searchIn = item.searchFields[key].toLowerCase();
			if (!searchFor.test(searchIn)) {
				showItem = showItem && false;
			}
		}
		if (false === showItem) {
			item.style.display = 'none';
		}
	});
}

export default filterListItems;
