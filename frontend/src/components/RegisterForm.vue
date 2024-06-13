<template>
  <div class="container">
    <div class="row justify-content-center mt-5">
      <div class="col-md-6">
        <div class="registration-container">
          <div class="icon-container">
            <i class="fas fa-hospital-alt register-icon"></i>
          </div>
          <form @submit.prevent="register" class="registration-form">
            <div class="form-group">
              <label for="name" class="form-label">Name:</label>
              <input type="text" v-model="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="email" class="form-label">Email:</label>
              <input type="text" v-model="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="password" class="form-label">Password:</label>
              <input type="password" v-model="password" id="password" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="userType" class="form-label">User Type:</label>
              <select id="userType" v-model="userType" class="form-select" required>
                <option value="admin">Admin</option>
                <option value="doctor">Doctor</option>
                <option value="patient">Patient</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
          </form>
          <div v-if="error" class="error">{{ error }}</div>
          <button @click="goToLogin" class="btn btn-success btn-block">Back</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      name: '',
      email: '',
      password: '',
      userType: '',
      error: ''
    };
  },
  methods: {
    async register() {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          userType: this.userType
        });
        console.log(response.data); // Log the response for debugging

        // Clear form fields
        this.name = '';
        this.email = '';
        this.password = '';
        this.userType = '';

        // Navigate to the desired route
        this.$router.push('/login');
      } catch (err) {
        if (err.response) {
          this.error = `Error: ${err.response.data.message}`;
          console.error(err.response.data);
        } else if (err.request) {
          this.error = 'No response from server. Please try again later.';
          console.error(err.request);
        } else {
          this.error = 'Request error. Please check your input and try again.';
          console.error('Error', err.message);
        }
      }
    },
    goToLogin() {
      // Navigate to the login page
      this.$router.push('/login');
    }
  }
};
</script>

<style>
.error {
  color: red;
}

.registration-container {
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background-color: #fff;
}

.registration-heading {
  margin-bottom: 20px;
  font-size: 24px;
  text-align: center;
}

.icon-container {
  text-align: center;
  margin-bottom: 20px;
}

.register-icon {
  font-size: 24px;
}

.form-group {
  margin-bottom: 20px;
}

.form-label {
  font-weight: bold;
  margin-bottom: 5px;
}

.form-control {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.form-select {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.btn-primary,
.btn-success {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-success:hover {
  background-color: #28a745;
}

.error {
  margin-top: 10px;
  text-align: center;
}
</style>
