<template>
  <DashboardView />

  <!-- Doctor Management Section -->
  <div class="doctor-management">
    <!-- Button to Add a New Doctor -->
    <button class="btn btn-info btn-sm mb-3" @click="addDoctor()">Add a Doctor</button>

    <!-- Table to Display Doctors -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Iterate over 'doctors' array to display each doctor -->
        <tr v-for="doctor in doctors" :key="doctor.id">
          <td>{{ doctor.id }}</td>
          <td>{{ doctor.name }}</td>
          <td>{{ doctor.email }}</td>
          <td>
            <!-- Buttons for Editing and Deleting Doctors -->
            <button class="btn btn-warning btn-sm" @click="editDoctor(doctor)">Edit</button>
            <button class="btn btn-danger btn-sm" @click="deleteDoctor(doctor)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Modal for Adding a New Doctor -->
  <Modal v-if="showAddDoctorModal" @close="showAddDoctorModal = false">
    <template v-slot:header>
      <h5>Add New Doctor</h5>
    </template>
    <template v-slot:body>
      <form @submit.prevent="postDoctor">
        <!-- Form fields for new doctor data -->
        <div class="mb-3">
          <label for="newDoctorName" class="form-label">Name</label>
          <input type="text" class="form-control" id="newDoctorName" v-model="newDoctorData.name">
        </div>
        <div class="mb-3">
          <label for="newDoctorEmail" class="form-label">Email</label>
          <input type="email" class="form-control" id="newDoctorEmail" v-model="newDoctorData.email">
        </div>
        <div class="mb-3">
          <label for="newDoctorPassword" class="form-label">Password</label>
          <input type="password" class="form-control" id="newDoctorPassword" v-model="newDoctorData.password">
        </div>
        <button type="submit" class="btn btn-primary">Add</button>
      </form>
    </template>
  </Modal>

  <!-- Modal for Editing an Existing Doctor -->
  <Modal v-if="showEditDoctorModal" @close="showEditDoctorModal = false">
    <template v-slot:header>
      <h5>Edit Doctor Details</h5>
    </template>
    <template v-slot:body>
      <form @submit.prevent="updateDoctor()">
        <!-- Form fields for editing existing doctor data -->
        <div class="mb-3">
          <label for="editDoctorName" class="form-label">Name</label>
          <input type="text" class="form-control" id="editDoctorName" v-model="editDoctorData.name">
        </div>
        <div class="mb-3">
          <label for="editDoctorEmail" class="form-label">Email</label>
          <input type="email" class="form-control" id="editDoctorEmail" v-model="editDoctorData.email">
        </div>
        <div class="mb-3">
          <label for="editDoctorPassword" class="form-label">Password</label>
          <input type="password" class="form-control" id="editDoctorPassword" v-model="editDoctorData.password">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
      </form>
    </template>
  </Modal>

</template>

<script>
import Modal from './PopUpModal.vue'; // Importing Modal component
import axios from 'axios'; // Importing Axios for HTTP requests
import DashboardView from '@/views/DashboardView.vue'; // Importing DashboardView component

export default {
  name: 'DoctorManagement', // Component name
  components: {
    Modal,
    DashboardView
  },
  data() {
    return {
      doctors: [], // Array to store list of doctors
      showAddDoctorModal: false, // Flag to control visibility of 'Add Doctor' modal
      showEditDoctorModal: false, // Flag to control visibility of 'Edit Doctor' modal
      newDoctorData: { // Object to store data for adding a new doctor
        name: '',
        email: '',
        password: '',
        userType: 'doctor'
      },
      editDoctorData: { // Object to store data for editing an existing doctor
        id: '',
        name: '',
        email: '',
        password: '',
        userType: 'doctor',
        updated_at: ''
      }
    };
  },
  mounted() {
    this.fetchDoctors(); // Fetch doctors data on component mount
  },
  methods: {
    fetchDoctors() {
      // Method to fetch list of doctors from API
      fetch('http://127.0.0.1:8000/api/doctor', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + localStorage.getItem('token') // Authorization token from local storage
        },
      })
        .then(response => response.json()) // Parse JSON response
        .then(data => {
          this.doctors = data.DoctorAccounts; // Update 'doctors' array with fetched data
        })
        .catch(err => {
          // Error handling for fetch request
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
        });
    },
    addDoctor() {
      // Method to show 'Add Doctor' modal
      this.showAddDoctorModal = true;
    },
    async postDoctor() {
      // Method to add a new doctor via API
      try {
        await axios.post('http://127.0.0.1:8000/api/doctor', this.newDoctorData, {
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + localStorage.getItem('token') // Authorization token from local storage
          }
        });
        this.showAddDoctorModal = false; // Close 'Add Doctor' modal on success
      } catch (error) {
        console.error('There was an error adding the product:', error); // Log error if request fails
      }
      this.fetchDoctors(); // Refresh doctors list after adding a new doctor
    },
    editDoctor(doctor) {
      // Method to populate 'Edit Doctor' modal with current doctor data
      this.editDoctorData = { ...doctor };
      this.showEditDoctorModal = true; // Show 'Edit Doctor' modal
    },
    async updateDoctor() {
      // Method to update existing doctor details via API
      this.editDoctorData.updated_at = new Date().toISOString(); // Update 'updated_at' timestamp
      await axios.put(`http://127.0.0.1:8000/api/doctor/${this.editDoctorData.id}`, this.editDoctorData, {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer ' + localStorage.getItem('token') // Authorization token from local storage
        }
      });
      this.showEditDoctorModal = false; // Close 'Edit Doctor' modal on success
      this.fetchDoctors(); // Refresh doctors list after updating
    },
    async deleteDoctor(doctor) {
      // Method to delete a doctor via API
      const index = this.doctors.findIndex(d => d.id === doctor.id);
      if (index !== -1) {
        this.doctors.splice(index, 1); // Remove doctor from local array
      }
      try {
        await axios.delete(`http://127.0.0.1:8000/api/doctor/${doctor.id}`, {
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + localStorage.getItem('token') // Authorization token from local storage
          }
        });
      } catch (error) {
        console.error('There was an error deleting the product:', error); // Log error if request fails
      }
    }
  }
};
</script>

<style scoped>
/* Scoped CSS styles for Doctor Management component */
.doctor-management {
  margin-top: 20px;
  padding: 20px;
  border-radius: 8px;
  background-color: #fff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Additional styling for tables, buttons, forms, etc. */
/* ... */
</style>
