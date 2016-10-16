/**
 * Contains the logic for the pause and resume button.
 *
 * @param graph the associated webvowl graph
 * @returns {{}}
 */
module.exports = function (graph) {

	var pauseMenu = {},
		pauseButton;


	/**
	 * Adds the pause button to the website.
	 */
	pauseMenu.setup = function () {

		if (document.getElementById('pause-button') === null) return;

		pauseButton = d3.select("#pause-button")
			.datum({paused: false})
			.on("click", function (d) {
				if (d.paused) {
					graph.unfreeze();
				} else {
					graph.freeze();
				}
				d.paused = !d.paused;
				updatePauseButton();
			});

		// Set these properties the first time manually
		updatePauseButton();
	};

	function updatePauseButton() {
		updatePauseButtonClass();
		updatePauseButtonText();
	}

	function updatePauseButtonClass() {
		if ( pauseButton === undefined ) return false;

		pauseButton.classed("paused", function (d) {
			return d.paused;
		});
	}

	function updatePauseButtonText() {
		if ( pauseButton === undefined ) return;

		if (pauseButton.datum().paused) {
			pauseButton.text("Resume");
		} else {
			pauseButton.text("Pause");
		}
	}

	pauseMenu.reset = function () {
		if ( pauseButton === undefined ) return;

		// Simulate resuming
		pauseButton.datum().paused = false;
		graph.unfreeze();
		updatePauseButton();
	};


	return pauseMenu;
};
