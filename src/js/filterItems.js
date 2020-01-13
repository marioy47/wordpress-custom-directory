
const filterListItems = (listItems, formValues) => {

	// Precompile the regular expresions.
	const regexps = {};
	for (const key in formValues) {
		regexps[key] = new RegExp(formValues[key].toLowerCase().trim().replace(/\s+/, ".+"), 'i');
	}

	// Loop trough each list item searching matches
	listItems.forEach(item => {
		item.style.display = 'initial';
		let showItem = true;

		// Loop for each form item looking for matches in the item.
		for (const key in regexps) {
			if ('undefined' == item.searchFields[key]) continue;
			if (!regexps[key].test(item.searchFields[key])) {
				showItem = showItem && false;
			}
		}
		if (false === showItem) {
			item.style.display = 'none';
		}
	});
}

export default filterListItems;
