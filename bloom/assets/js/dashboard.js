// Day 10: Search, filter and sort existing PHP-generated cards.
const searchInput = document.getElementById('searchBirthdays');
const relationshipFilter = document.getElementById('relationshipFilter');
const sortSelect = document.getElementById('sortBirthdays');
const resetButton = document.getElementById('resetFilters');
const list = document.getElementById('birthdayList');
const cards = Array.from(document.querySelectorAll('#birthdayList .birthday-card'));
const emptyState = document.getElementById('emptyState');
const filterCount = document.getElementById('filterCount');

function refreshBirthdays() {
    const term = searchInput.value.trim().toLocaleLowerCase();
    const relationship = relationshipFilter.value;
    const sort = sortSelect.value;
    const visibleCards = cards.filter(card => {
        const searchable = `${card.dataset.name} ${card.dataset.notes}`.toLocaleLowerCase();
        const match = searchable.includes(term) &&
            (relationship === 'all' || card.dataset.relationship === relationship);
        card.hidden = !match;
        return match;
    });
    visibleCards.sort((a, b) => {
        switch (sort) {
            case 'name-asc': return a.dataset.name.localeCompare(b.dataset.name);
            case 'name-desc': return b.dataset.name.localeCompare(a.dataset.name);
            case 'month': return Number(a.dataset.month) - Number(b.dataset.month) || Number(a.dataset.day) - Number(b.dataset.day) || a.dataset.name.localeCompare(b.dataset.name);
            case 'recent': return b.dataset.created.localeCompare(a.dataset.created) || Number(b.dataset.id) - Number(a.dataset.id);
            default: return Number(a.dataset.days) - Number(b.dataset.days) || a.dataset.name.localeCompare(b.dataset.name);
        }
    });
    visibleCards.forEach(card => list.appendChild(card));
    emptyState.hidden = visibleCards.length > 0;
    emptyState.textContent = cards.length === 0
        ? 'No birthdays yet. Click Add Birthday to get started!'
        : 'No birthdays match your search and filters.';
    filterCount.textContent = `Showing ${visibleCards.length} of ${cards.length} birthdays`;
}
if (searchInput && relationshipFilter && sortSelect && list && emptyState) {
    searchInput.addEventListener('input', refreshBirthdays);
    relationshipFilter.addEventListener('change', refreshBirthdays);
    sortSelect.addEventListener('change', refreshBirthdays);
    resetButton.addEventListener('click', () => {
        searchInput.value = '';
        relationshipFilter.value = 'all';
        sortSelect.value = 'upcoming';
        refreshBirthdays();
        searchInput.focus();
    });
    refreshBirthdays();
}
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', event => {
        if (!window.confirm('Delete this birthday permanently?')) event.preventDefault();
    });
});
