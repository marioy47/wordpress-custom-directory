/**
 * Helper function to delay the search after a couple of milescons hav passed after the last keypress
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
