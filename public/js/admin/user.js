let sortBy = document.querySelector('#sortBy');
let nbItems = document.querySelector('#itemsPerPage');
const url = new URL(window.location);
const form = document.querySelector('#search-form');
const searchParams = url.searchParams;

sortBy.addEventListener('change', (event) => {
    event.preventDefault();

    const criteria = event.target.value;

    if (criteria !== '') {
        searchParams.set(`sortBy`, criteria);
    } else {
        searchParams.delete(`sortBy`);
    }

    return window.location = url.href;

});

nbItems.addEventListener('change', (event) => {
    event.preventDefault();

    const criteria = event.target.value;

    searchParams.forEach((value, key) => searchParams.delete(key));

    if (criteria !== '') {
        searchParams.set(`items`, criteria);

    } else {
        searchParams.delete(`items`);
    }

    return window.location = url.href;

});

form.addEventListener('submit', (event) => {
    event.preventDefault();

    searchParams.forEach((value, key) => searchParams.delete(key));

    let query = event.target.querySelector('#search').value;

    if (query !== '') {
        searchParams.set('search', query);
    } else {
        searchParams.delete(`search`);
    }

    return window.location = url.href;
});