// Browser-side validation improves usability. PHP validates again on the server.
const birthdayForm = document.getElementById('birthdayForm');
if (birthdayForm) {
  birthdayForm.addEventListener('submit', event => {
    const name = document.getElementById('person_name').value.trim();
    const dateValue = document.getElementById('birth_date').value;
    const notes = document.getElementById('notes').value;
    if (!name || name.length > 100 || !dateValue || notes.length > 2000) {
      event.preventDefault(); alert('Check the name, birth date and notes before saving.'); return;
    }
    const selected = new Date(dateValue + 'T12:00:00');
    // const today = new Date(); today.setHours(23,59,59,999);
    // if (Number.isNaN(selected.getTime()) || selected > today) {
    //   event.preventDefault(); alert('Enter a valid birth date that is not in the future.');
    // }

    function validateBirthday(){
      const birthdayInput = document.getElementById("birth_date");
      const selectedDate = birthdayInput.value;
      if (!selectedDate) {
        alert("Please select a birthday date.");
        return false;
      }

      const date = new Date(selectedDate + "T00:00:00");

      if (isNaN(date.getTime())) {
        alert("Please enter a valid birthday date.");
        return false;
      }
      return true;
    }
  });
}
