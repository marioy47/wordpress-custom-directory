
const searchList = (listItems, formValues) => {

	// Loop trough each list item searching matches
	listItems.forEach(item => {
		item.style.display = 'initial';
		let showItem = true;

		// Loop for each form item looking for matches in the item.
		for (const key in formValues) {
			if ('undefined' == item.searchFields[key]) continue;
			const searchFor = formValues[key].toLowerCase().trim();
			const searchIn = item.searchFields[key].toLowerCase();
			if (!searchIn.includes(searchFor)) {
				showItem = showItem && false;
			}
		}
		if (false === showItem) {
			item.style.display = 'none';
		}
	});
}

export default searchList;
