function submitForm(event, formType) {
    event.preventDefault();

    const formElement = event.target;
    const formData = new FormData(formElement);
    formData.append('form_type', formType);

    fetch('../newsletter.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Form submitted successfully!');
            formElement.reset();
        } else {
            //alert(`An error occurred: ${data.error}`);
            console.error('Network error:', data.error);
        }
    })
    .catch(error => {
        //alert('A network error occurred while submitting the form.');
        console.error('Network error:', error);
    });
}