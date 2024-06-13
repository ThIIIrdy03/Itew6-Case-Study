<template>
  <div>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <router-link class="navbar-brand mr-4" to="/dashboard">
        <i class="fas fa-hospital"></i> 
      </router-link>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav">
          <li v-if="isAdmin" class="nav-item">
            <router-link class="nav-link" to="/doctors">Manage Doctor</router-link>
          </li>
          <li v-if="isAdmin" class="nav-item">
            <router-link class="nav-link" to="/patients">Manage Patient</router-link>
          </li>
          <li v-if="isAdmin" class="nav-item">
            <router-link class="nav-link" to="/appointments">Manage Appointment</router-link>
          </li>
          <li v-if="isAdmin" class="nav-item">
            <router-link class="nav-link" to="/medicalrecords">Manage Records</router-link>
          </li>
          <li v-if="isAdmin" class="nav-item">
            <router-link class="nav-link" to="/users">Manage Users</router-link>
          </li>
          <li v-if="isDoctor || isPatient" class="nav-item">
            <router-link class="nav-link" to="/patients">Patients</router-link>
          </li>
          <li v-if="isDoctor || isPatient" class="nav-item">
            <router-link class="nav-link" to="/appointments">Appointments</router-link>
          </li>
          <li v-if="isDoctor || isPatient" class="nav-item">
            <router-link class="nav-link" to="/medicalrecords">Medical Records</router-link>
          </li>
        </ul>
      </div>
      <div class="ml-auto">
        <ul class="navbar-nav">
          <li>
            <router-link v-if="isDoctor || isAdmin" class="btn btn-danger mr-2" to="/logout">Logout</router-link>
            <router-link v-if="isPatient" class="btn btn-danger" to="/logout">Logout</router-link>
          </li>
        </ul>
      </div>
    </nav>
    <!-- Main Content -->
    <div class="container mt-3">
      <router-view />
    </div>
  </div>
</template>




<script>
import axios from 'axios';

export default {
  name: 'DashBoard',
  data() {
    return {
      user: null,
      showeditUserModal: false,
      editUserData: {
        id: '',
        name: '',
        email: '',
        password: '',
        userType: '',
        updated_at: ''
      },
    };
  },
  computed: {
    isAdmin() {
      return this.user && this.user.userType === 'admin';
    },
    isDoctor() {
      return this.user && this.user.userType === 'doctor';
    },
    isPatient() {
      return this.user && this.user.userType === 'patient';
    },
  },
  created() {
    this.loadUserFromLocalStorage();
  },
  methods: {
    loadUserFromLocalStorage() {
      const user = localStorage.getItem('user');
      if (user) {
        this.user = JSON.parse(user);
        this.editUserData = {...this.user};
      }
    },
    viewprofile() {
      this.showeditUserModal = true;
    },
    async updateUser() {
      this.editUserData.updated_at = new Date().toISOString();
      await axios.put(`http://127.0.0.1:8000/api/doctor/${this.editUserData.id}`, this.editUserData, {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + localStorage.getItem('token')
        }
      });
      this.showeditUserModal = false;
    },
  },
};
</script>


<style scoped>

</style>
