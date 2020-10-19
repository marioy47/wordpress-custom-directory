/**
 * Helper function to delay the search after a couple of milescons hav passed after the last keypress
 *
 * @param {Function} fn The function to debounce.
 * @param {number} time Number of miliseconds to debounce.
 */
const debounce = (fn, time) => {
	let timeout;

	return function () {
		const functionCall = () => fn.apply(this, arguments);

		clearTimeout(timeout);
		timeout = setTimeout(functionCall, time);
	};
};

export default debounce;
