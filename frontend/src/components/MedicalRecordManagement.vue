<template>
    <!-- Dashboard view component -->
    <DashboardView />
    <div class="container">
        <div class="hello">
            <!-- Button to add medical record -->
            <button v-if="!isPatient && !isAdmin" class="btn btn-info btn-sm m-1" @click="addMedicalRecord()">Add Medical Record</button>
            <!-- Table to display medical records -->
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Patient Name</th>
                        <th>Details</th>
                        <th v-if="isAdmin || isDoctor">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Iterate through records -->
                    <tr v-for="record in records" :key="record.id">
                        <td>{{ record.id }}</td>
                        <td>{{ record.PatientName }}</td>
                        <td>{{ record.RecordDetails }}</td>
                        <td>
                            <!-- Edit button for admin and doctor -->
                            <button v-if="isAdmin || isDoctor" class="btn btn-warning btn-sm m-1" @click="editMedicalRecord(record)">Edit</button>
                            <!-- Delete button for admin -->
                            <button v-if="isAdmin" class="btn btn-danger btn-sm m-1" @click="deleteMedicalRecord(record)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal for adding new medical record -->
        <Modal v-if="showAddMedicalRecord" @close="showAddMedicalRecord = false">
            <template v-slot:header>
                <h5>Add New Medical Record</h5>
            </template>
            <template v-slot:body>
                <!-- Form for adding new medical record -->
                <form @submit.prevent="postMedicalRecord()" class="registration-form">
                    <div class="form-group">
                        <label for="patientName">Patient Name</label>
                        <!-- Dropdown to select patient -->
                        <select id="patientName" class="form-control" v-model="newMedicalRecordData.patientID" required>
                            <option value="" disabled selected>Select Patient</option>
                            <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.name }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="recordDetails">Record Details:</label>
                        <!-- Textarea for record details -->
                        <textarea type="text" class="form-control" id="recordDetails" v-model="newMedicalRecordData.RecordDetails"></textarea>
                    </div>
                    <!-- Button to add record -->
                    <button type="submit" class="btn btn-primary m-1">Add Record</button>
                </form>
                <!-- Error message display -->
                <div v-if="error" class="error">{{ error }}</div>
            </template>
        </Modal>

        <!-- Modal for editing medical record -->
        <Modal v-if="showEditMedicalRecord" @close="showEditMedicalRecord = false">
            <template v-slot:header>
                <h5>Edit Medical Record</h5>
            </template>
            <template v-slot:body>
                <!-- Form for editing medical record -->
                <form @submit.prevent="updateMedicalRecord()" class="registration-form">
                    <div class="form-group">
                        <label for="patientName">Patient Name</label>
                        <!-- Dropdown to select patient -->
                        <select id="patientName" class="form-control" v-model="editMedicalRecordData.patientID" required>
                            <option value="" disabled selected>Select Patient</option>
                            <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.name }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="recordDetails">Record Details:</label>
                        <!-- Textarea for record details -->
                        <textarea type="text" class="form-control" id="recordDetails" v-model="editMedicalRecordData.RecordDetails"></textarea>
                    </div>
                    <!-- Button to update record -->
                    <button type="submit" class="btn btn-primary m-1">Update Record</button>
                </form>
            </template>
        </Modal>
    </div>
</template>
  

<script>
import Modal from './PopUpModal.vue';
import axios from 'axios';
import DashboardView from '@/views/DashboardView.vue';

export default {
    name: 'DoctorManagement',
    components: {
        Modal,
        DashboardView
    },
    data() {
        return {
            records: [],
            patients: [],
            user: '',
            showAddMedicalRecord: false,
            showEditMedicalRecord: false,
            newMedicalRecordData: {
                patientID: '',
                RecordDetails: ''
            },
            editMedicalRecordData: {
                id: '',
                patientID: '',
                RecordDetails: ''
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
    mounted() {
        this.fetchAllMedicalRecords();
        this.fetchPatients();
    },
    created() {
        this.loadUserFromLocalStorage();
    },
    methods: {
        fetchAllMedicalRecords() {
            if (this.isAdmin) {
                fetch('http://127.0.0.1:8000/api/medical_records', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        this.records = data.MedicalRecords;
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
            } else if (this.isPatient) {
                fetch('http://127.0.0.1:8000/api/medical_records?patientID=' + this.user.id, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        this.records = data.MedicalRecords;
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
            } else if (this.isDoctor) {
                fetch('http://127.0.0.1:8000/api/medical_records', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        this.records = data.MedicalRecords;
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
            }
        },
        fetchPatients() {
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
        addMedicalRecord() {
            this.showAddMedicalRecord = true;
        },
        async postMedicalRecord() {
            try {
                await axios.post('http://127.0.0.1:8000/api/medical_records', this.newMedicalRecordData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                this.showAddMedicalRecord = false;
            } catch (error) {
                console.error('There was an error adding the product:', error);
            }
            this.fetchAllMedicalRecords();
            this.newMedicalRecordData = [];
            this.showAddMedicalRecord = false;
        },
        editMedicalRecord(record) {
            this.editMedicalRecordData = { ...record };
            this.showEditMedicalRecord = true;
        },
        async updateMedicalRecord() {
            this.editMedicalRecordData.updated_at = new Date().toISOString();
            await axios.put(`http://127.0.0.1:8000/api/medical_records/${this.editMedicalRecordData.id}`, this.editMedicalRecordData, {
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                }
            });
            this.showEditMedicalRecord = false;
            this.fetchAllMedicalRecords();
        },
        async deleteMedicalRecord(record) {
            const index = this.records.findIndex(r => r.id === record.id);
            if (index !== -1) {
                this.records.splice(index, 1);
            }
            try {
                await axios.delete(`http://127.0.0.1:8000/api/medical_records/${record.id}`, {
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
            }
        },
    }
};
</script>
