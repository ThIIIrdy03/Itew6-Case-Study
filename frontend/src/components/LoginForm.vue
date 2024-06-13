<template>
  <div class="login-container">
    <div class="login-form">
      <div class="icon-container">
        <i class="fas fa-hospital-alt login-icon"></i>
      </div>
      <form @submit.prevent="login">
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input type="email" v-model="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <input type="password" v-model="password" id="password" class="form-control" required>
        </div>
        <router-link to="/register" class="btn btn-primary">Register</router-link>
        <button type="submit" class="btn btn-success">Login</button>
      </form>
      <div v-if="error" class="error">{{ error }}</div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      password: '',
      error: ''
    };
  },
  methods: {
    async login() {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/login', {
          email: this.email,
          password: this.password
        });
        const token = response.data.token;
        const user = response.data.user;
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(user));
        this.$router.push('/dashboard');
      } catch (err) {
        this.error = 'Login failed. Please check your credentials and try again.';
      }
    }
  }
};
</script>

<style>
:root {
  --primary-color: #007bff;
  --primary-color-hover: #0056b3;
  --success-color: #28a745;
  --success-color-hover: #218838;
  --border-color: #ccc;
  --background-color: #fff;
  --error-color: red;
  --text-color: #000; /* Black color for text */
}

body {
  color: var(--text-color); /* Apply black color to the body text */
}

.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-color: var(--background-color);
}

.login-form {
  max-width: 400px;
  width: 100%;
  padding: 20px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  background-color: var(--background-color);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.icon-container {
  text-align: center;
  margin-bottom: 10px;
}

.login-icon {
  font-size: 50px;
  color: var(--text-color); /* Black color for the icon */
}

.login-heading {
  margin-bottom: 20px;
  font-size: 24px;
  text-align: center;
  color: var(--primary-color);
}

.form-group {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
  color: var(--text-color); /* Black color for labels */
}

.form-control {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid var(--border-color);
  border-radius: 4px;
  transition: border-color 0.3s ease;
  color: var(--text-color); /* Black color for input text */
}

.form-control:focus {
  border-color: var(--primary-color);
}

.btn {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  margin-bottom: 10px;
}

.btn-primary {
  background-color: var(--primary-color);
  color: #fff;
}

.btn-primary:hover {
  background-color: var(--primary-color-hover);
}

.btn-success {
  background-color: var(--success-color);
  color: #fff;
}

.btn-success:hover {
  background-color: var(--success-color-hover);
}

.error {
  color: var(--error-color);
  margin-top: 10px;
  text-align: center;
}

.existing-account {
  text-align: center;
  margin-top: 20px;
}

.existing-account-link {
  color: var(--primary-color);
  text-decoration: underline;
  font-weight: bold;
}
</style>
