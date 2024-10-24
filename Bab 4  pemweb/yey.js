// Get the input fields and button elements
const emailInput = document.querySelector('input[placeholder="Email"]');
const usernameInput = document.querySelector('input[placeholder="Username"]');
const passwordInput = document.querySelector('input[placeholder="Password"]');
const registerButton = document.querySelector('button');

// Add an event listener to the register button
registerButton.addEventListener('click', (e) => {
  e.preventDefault();

  // Get the input values
  const email = emailInput.value;
  const username = usernameInput.value;
  const password = passwordInput.value;

  // Validate the input values (you can add more validation logic here)
  if (!email || !username || !password) {
    alert('Please fill in all the fields');
    return;
  }

  // Send a request to your server to register the user (you can use fetch or XMLHttpRequest)
  fetch('/register', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      email,
      username,
      password
    })
  })
  .then(response => response.json())
  .then((data) => {
    if (data.success) {
      // Registration successful, redirect to login page or show a success message
      alert('Registration successful!');
      // You can also redirect to a login page or another page here
    } else {
      // Registration failed, show an error message
      alert('Registration failed: ' + data.error);
    }
  })
  .catch((error) => {
    console.error('Error registering user:', error);
  });
});