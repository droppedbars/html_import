/**
 * Created by patrick on 14-04-26.
 */
function htim_addCategoryOnClick(catNameArray, catValueArray) {
	var rowCounter = document.getElementById('categoryRowCounter');

	var rowName = 'cat_' + rowCounter.value;

	var newRemoveButton = document.createElement('input');
	newRemoveButton.setAttribute('type', 'button');
	newRemoveButton.setAttribute('class', 'button-primary');
	newRemoveButton.setAttribute('value', '-');
	newRemoveButton.setAttribute('onclick', 'htim_removeCategoryOnClick(\'cat_' + rowCounter.value + '\')');

	// this is the stuff
	var newSelect = document.createElement('select');
	newSelect.setAttribute('name', 'category_' + rowCounter.value);


	for (var i=0; i < catNameArray.length; i++)
	{
		var option = document.createElement('option');
		option.setAttribute('value', catNameArray[i]);
		option.text = catValueArray[i];

		newSelect.appendChild(option);

	}

	// this is the stuff

	var newBR = document.createElement('br');

	var newRowSpan = document.createElement('span');
	newRowSpan.setAttribute('id', 'cat_' + rowCounter.value);
	newRowSpan.appendChild(newRemoveButton);
	newRowSpan.appendChild(document.createTextNode(' '));
	newRowSpan.appendChild(newSelect);
	newRowSpan.appendChild(document.createTextNode(' '));
	newRowSpan.appendChild(newBR);

	var mainDiv = document.getElementById('settings-categories');
	mainDiv.appendChild(newRowSpan);

	rowCounter.value = ++rowCounter.value;
}

function htim_removeCategoryOnClick(row) {
	var mainDiv = document.getElementById('settings-categories');
	var theRow = document.getElementById(row);
	mainDiv.removeChild(theRow);
}