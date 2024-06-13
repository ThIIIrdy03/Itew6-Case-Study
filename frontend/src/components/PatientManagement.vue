<template>
    <DashboardView />
    <div class="hello">
        <!-- Button to add a new patient -->
        <button class="btn btn-info btn-sm m-1" @click="addPatient()">Add a Patient</button>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="patient in patients" :key="patient.id">
                    <td>{{ patient.id }}</td>
                    <td>{{ patient.name }}</td>
                    <td>{{ patient.email }}</td>
                    <td>
                        <!-- Edit button for admins and doctors -->
                        <button v-if="isAdmin || isDoctor" class="btn btn-warning btn-sm m-1" @click="editPatient(patient)">Edit</button>
                        <!-- Delete button for admins only -->
                        <button v-if="isAdmin" class="btn btn-danger btn-sm m-1" @click="deletePatient(patient)">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal for adding a new patient -->
    <Modal v-if="showAddPatientModal" @close="showAddPatientModal = false">
        <template v-slot:header>
            <h5>Add New Patient</h5>
        </template>
        <template v-slot:body>
            <form @submit.prevent="postPatient()">
                <div class="form-group">
                    <label for="newPatientName">Name</label>
                    <input type="text" class="form-control" id="newPatientName" v-model="newPatientData.name">
                </div>
                <div class="form-group">
                    <label for="newPatientEmail">Email</label>
                    <input type="text" class="form-control" id="newPatientEmail" v-model="newPatientData.email">
                </div>
                <div class="form-group">
                    <label for="newPatientPassword">Password</label>
                    <input type="password" class="form-control" id="newPatientPassword" v-model="newPatientData.password">
                </div>
                <button type="submit" class="btn btn-primary">Add</button>
            </form>
        </template>
    </Modal>

    <!-- Modal for editing patient details -->
    <Modal v-if="showEditPatientModal" @close="showEditPatientModal = false">
        <template v-slot:header>
            <h5>Edit Patient Details</h5>
        </template>
        <template v-slot:body>
            <form @submit.prevent="updatePatient()">
                <div class="form-group">
                    <label for="newPatientName">Name</label>
                    <input type="text" class="form-control" id="newPatientName" v-model="editPatientData.name">
                </div>
                <div class="form-group">
                    <label for="newPatientEmail">Email</label>
                    <input type="text" class="form-control" id="newPatientEmail" v-model="editPatientData.email">
                </div>
                <div class="form-group">
                    <label for="newPatientPassword">Password</label>
                    <input type="password" class="form-control" id="newPatientPassword" v-model="editPatientData.password">
                </div>
                <button type="submit" class="btn btn-primary m-1">Update</button>
            </form>
        </template>
    </Modal>
</template>

<script>
import Modal from './PopUpModal.vue';
import axios from 'axios';
import DashboardView from '@/views/DashboardView.vue';

export default {
    name: 'PatientManagement',
    components: {
        Modal,
        DashboardView
    },
    data() {
        return {
            patients: [], // List of patient records
            user: '', // Current logged-in user
            showAddPatientModal: false, // Control visibility of Add Patient modal
            showEditPatientModal: false, // Control visibility of Edit Patient modal
            newPatientData: {
                name: '',
                email: '',
                password: '',
                userType: 'patient' // Default userType to 'patient'
            },
            editPatientData: {
                id: '',
                name: '',
                email: '',
                password: '',
                userType: 'patient',
                updated_at: ''
            }
        };
    },
    computed: {
        isAdmin() {
            return this.user && this.user.userType === 'admin'; // Check if the user is an admin
        },
        isDoctor() {
            return this.user && this.user.userType === 'doctor'; // Check if the user is a doctor
        },
        isPatient() {
            return this.user && this.user.userType === 'patient'; // Check if the user is a patient
        },
    },
    mounted() {
        this.fetchPatient(); // Fetch patient data when component is mounted
    },
    created() {
        this.loadUserFromLocalStorage(); // Load user data from local storage when component is created
    },
    methods: {
        fetchPatient() {
            fetch('http://127.0.0.1:8000/api/patient', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
            })
                .then(response => response.json())
                .then(data => {
                    this.patients = data.PatientAccounts; // Store fetched patient data
                })
                .catch(err => {
                    // Handle errors
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
        addPatient() {
            this.showAddPatientModal = true; // Show Add Patient modal
        },
        async postPatient() {
            try {
                await axios.post('http://127.0.0.1:8000/api/patient', this.newPatientData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                this.showAddPatientModal = false; // Hide Add Patient modal
            } catch (error) {
                console.error('There was an error adding the product:', error); // Log error
            }
            this.fetchPatient(); // Refresh patient data
        },
        editPatient(patient) {
            this.editPatientData = { ...patient }; // Copy patient data for editing
            this.showEditPatientModal = true; // Show Edit Patient modal
        },
        async updatePatient() {
            this.editPatientData.updated_at = new Date().toISOString(); // Set updated_at timestamp
            await axios.put(`http://127.0.0.1:8000/api/patient/${this.editPatientData.id}`, this.editPatientData, {
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                }
            });
            this.showEditPatientModal = false; // Hide Edit Patient modal
            this.fetchPatient(); // Refresh patient data
        },
        async deletePatient(patient) {
            const index = this.patients.findIndex(p => p.id === patient.id);
            if (index !== -1) {
                this.patients.splice(index, 1); // Remove patient from the list
            }
            try {
                await axios.delete(`http://127.0.0.1:8000/api/patient/${patient.id}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
            } catch (error) {
                console.error('There was an error deleting the product:', error); // Log error
            }
        },
        loadUserFromLocalStorage() {
            const user = localStorage.getItem('user');
            if (user) {
                this.user = JSON.parse(user); // Parse and store user data from local storage
                this.editUserData = { ...this.user }; // Copy user data for editing
            }
        },
    }
};
</script>
