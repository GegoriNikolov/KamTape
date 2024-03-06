var UT_RATING_IMG = 'img/star.gif';
var UT_RATING_IMG_HOVER = 'img/star_hover.gif';
var UT_RATING_IMG_HALF = 'img/star_half.gif';
var UT_RATING_IMG_BG = 'img/star_bg.gif';
var UT_RATING_IMG_REMOVED = 'img/star_removed.gif';

function UTRating(ratingElementId, maxStars, objectName, formName)
{
	this.ratingElementId = ratingElementId;
	this.maxStars = maxStars;
	this.objectName = objectName;
	this.formName = formName;

	this.starTimer = null;
	this.starCount = 0;

	// pre-fetch image
	(new Image()).src = UT_RATING_IMG;
	(new Image()).src = UT_RATING_IMG_HALF;

	function showStars(starNum) {
		this.clearStarTimer();
		this.greyStars();
		this.colorStars(starNum);
		this.setMessage(starNum);
	}

	function setMessage(starNum) {
		messages = new Array("Rate this video!", "Poor", "Nothing special", "Worth watching", "Pretty cool", "Awesome!");
		document.getElementById('ratingMessage').innerHTML = messages[starNum];
	}

	function colorStars(starNum) {
		for (var i=1; i <= starNum; i++)
			document.getElementById('star_' + i).src = UT_RATING_IMG;
	}

	function greyStars() {
		for (var i=1; i < this.maxStars + 1; i++)
			if (i <= this.starCount)
				document.getElementById('star_' + i).src = UT_RATING_IMG_REMOVED;
			else
				document.getElementById('star_' + i).src = UT_RATING_IMG_BG;
	}

	function setStars(starNum) {
		this.starCount = starNum;
		this.drawStars(starNum);
		document.forms[this.formName]['rating'].value = this.starCount;
		var ratingElementId = this.ratingElementId;
		postForm(this.formName, true, function (req) { replaceDivContents(req, ratingElementId); });
	}

	function drawStars(starNum) {
		this.clearStarTimer();
		this.showStars(starNum);
	}

	function clearStars() {
		this.starTimer = setTimeout(this.objectName + ".resetStars()", 300);
	}

	function resetStars() {
		this.clearStarTimer();
		if (this.starCount)
			this.drawStars(this.starCount);
		else
			this.greyStars();
		this.setMessage(0);
	}

	function clearStarTimer() {
		if (this.starTimer) {
			clearTimeout(this.starTimer);
			this.starTimer = null;
		}
	}

	this.clearStars = clearStars;
	this.clearStarTimer = clearStarTimer;
	this.greyStars = greyStars;
	this.colorStars = colorStars;
	this.resetStars = resetStars;
	this.setStars = setStars;
	this.drawStars = drawStars;
	this.showStars = showStars;
	this.setMessage = setMessage;

}

