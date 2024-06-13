<template>
    <DashboardView />
    <div class="hello">
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
                        <button v-if="isAdmin || isDoctor" class="btn btn-warning btn-sm m-1"
                            @click="editPatient(patient)">Edit</button>
                        <button v-if="isAdmin" class="btn btn-danger btn-sm m-1"
                            @click="deletePatient(patient)">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

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
                    <input type="password" class="form-control" id="newPatientPassword"
                        v-model="newPatientData.password">
                </div>
                <button type="submit" class="btn btn-primary">Add</button>
            </form>
        </template>
    </Modal>

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
                    <input type="password" class="form-control" id="newPatientPassword"
                        v-model="editPatientData.password">
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
            patients: [],
            user: '',
            showAddPatientModal: false,
            showEditPatientModal: false,
            newPatientData: {
                name: '',
                email: '',
                password: '',
                userType: 'patient'
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
            return this.user && this.user.userType === 'admin';
        },
        isDoctor() {
            return this.user && this.user.userType === 'doctor';
        },
        isPatient() {
            return this.user && this.user.userType === 'patient';
        },
    },
    mounted() {
        this.fetchPatient();
    },
    created() {
        this.loadUserFromLocalStorage();
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
                    this.patients = data.PatientAccounts;
                })
                .catch(err => {
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
            this.showAddPatientModal = true;
        },
        async postPatient() {
            try {
                await axios.post('http://127.0.0.1:8000/api/patient', this.newPatientData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                this.showAddPatientModal = false;
            } catch (error) {
                console.error('There was an error adding the product:', error);
            }
            this.fetchPatient();
        },
        editPatient(patient) {
            this.editPatientData = { ...patient };
            this.showEditPatientModal = true;
        },
        async updatePatient() {
            this.editPatientData.updated_at = new Date().toISOString();
            await axios.put(`http://127.0.0.1:8000/api/patient/${this.editPatientData.id}`, this.editPatientData, {
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                }
            });
            this.showEditPatientModal = false;
            this.fetchPatient();
        },
        async deletePatient(patient) {
            const index = this.patients.findIndex(p => p.id === patient.id);
            if (index !== -1) {
                this.patients.splice(index, 1);
            }
            try {
                await axios.delete(`http://127.0.0.1:8000/api/patient/${patient.id}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
            } catch (error) {
                console.error('There was an error deleting the product:', error);
            }
        },
        loadUserFromLocalStorage() {
            const user = localStorage.getItem('user');
            if (user) {
                this.user = JSON.parse(user);
                this.editUserData = { ...this.user };
            }
        },
    }
};
</script>
