/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

const addForm = (event) => {
    const collectionHolder = document.querySelector(event.currentTarget.dataset.collectionHolderId);
    // create new div element
    const item = document.createElement('div');
    // replace "__name__" in the prototype's HTML to unique index
    item.innerHTML = collectionHolder.dataset.prototype.replace(
        /__name__/g,
        collectionHolder.dataset.index
    );
    // attach event listener to remove button
    item.querySelector('.btn-remove-item').addEventListener('click', () => item.remove());
    // append new div element to collection holder
    collectionHolder.appendChild(item);
    // increment index
    collectionHolder.dataset.index++;
};

const closeAlert = (event) => {
    // remove parent element
    event.currentTarget.parentElement.remove();
};

if (document.querySelector('.close-alert')) {
    document.querySelectorAll('.close-alert').forEach((btn) => {
        // attach event listener to each close alert button
        btn.addEventListener('click', closeAlert);
    });
}

if (document.querySelector('.btn-add-item')) {
    // attach event listener to add button
    document.querySelector('.btn-add-item').addEventListener('click', addForm);
}
