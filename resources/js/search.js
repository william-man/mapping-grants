const form = document.getElementById('postcode-search');

form.addEventListener('submit', function (event) {
    event.preventDefault()
    
    const postcodeInput = document.getElementById('postcode-input');
    console.log(postcodeInput.value);
})