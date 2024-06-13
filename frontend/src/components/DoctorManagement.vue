<template>
  <DashboardView />
  
    
    <div class="doctor-management">
      <button class="btn btn-info btn-sm mb-3" @click="addDoctor()">Add a Doctor</button>
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
          <tr v-for="doctor in doctors" :key="doctor.id">
            <td>{{ doctor.id }}</td>
            <td>{{ doctor.name }}</td>
            <td>{{ doctor.email }}</td>
            <td>
              <button class="btn btn-warning btn-sm" @click="editDoctor(doctor)">Edit</button>
              <button class="btn btn-danger btn-sm" @click="deleteDoctor(doctor)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Modal v-if="showAddDoctorModal" @close="showAddDoctorModal = false">
      <template v-slot:header>
        <h5>Add New Doctor</h5>
      </template>
      <template v-slot:body>
        <form @submit.prevent="postDoctor">
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

    <Modal v-if="showEditDoctorModal" @close="showEditDoctorModal = false">
      <template v-slot:header>
        <h5>Edit Doctor Details</h5>
      </template>
      <template v-slot:body>
        <form @submit.prevent="updateDoctor()">
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
            doctors: [],
            showAddDoctorModal: false,
            showEditDoctorModal: false,
            newDoctorData: {
                name: '',
                email: '',
                password: '',
                userType: 'doctor'
            },
            editDoctorData: {
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
        this.fetchDoctors();
    },
    methods: {
        fetchDoctors() {
            fetch('http://127.0.0.1:8000/api/doctor', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
            })
                .then(response => response.json())
                .then(data => {
                    this.doctors = data.DoctorAccounts;
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
        addDoctor() {
            this.showAddDoctorModal = true;
        },
        async postDoctor() {
            try {
                await axios.post('http://127.0.0.1:8000/api/doctor', this.newDoctorData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                this.showAddDoctorModal = false;
            } catch (error) {
                console.error('There was an error adding the product:', error);
            }
            this.fetchDoctors();
        },
        editDoctor(doctor) {
            this.editDoctorData = { ...doctor };
            this.showEditDoctorModal = true;
        },
        async updateDoctor() {
            this.editDoctorData.updated_at = new Date().toISOString();
            await axios.put(`http://127.0.0.1:8000/api/doctor/${this.editDoctorData.id}`, this.editDoctorData, {
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                }
            });
            this.showEditDoctorModal = false;
            this.fetchDoctors();
        },
        async deleteDoctor(doctor) {
            const index = this.doctors.findIndex(d => d.id === doctor.id);
            if (index !== -1) {
                this.doctors.splice(index, 1);
            }
            try {
                await axios.delete(`http://127.0.0.1:8000/api/doctor/${doctor.id}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
            } catch (error) {
                console.error('There was an error deleting the product:', error);
            }
        }
    }
};
</script>

<style scoped>
.doctor-management {
  margin-top: 20px;
  padding: 20px;
  border-radius: 8px;
  background-color: #fff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.doctor-management h1 {
  font-size: 24px;
  margin-bottom: 20px;
}

.btn-info {
  background-color: #4caf50;
  border-color: #4caf50;
}

.btn-info:hover {
  background-color: #388e3c;
  border-color: #388e3c;
}

.table {
  width: 100%;
  margin-bottom: 20px;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.table th,
.table td {
  padding: 15px;
  text-align: left;
  border-bottom: 1px solid #dee2e6;
}

.table th {
  background-color: #f8f9fa;
  font-weight: bold;
}

.btn-warning,
.btn-danger {
  padding: 8px 15px;
}

.modal-body {
  padding: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-label {
  font-weight: bold;
}

.form-control {
  width: 100%;
  padding: 12px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.btn-primary {
  padding: 12px 20px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.error {
  color: red;
  margin-top: 10px;
  text-align: center;
}

.login-link {
  display: block;
  text-align: center;
  margin-top: 20px;
  text-decoration: none;
  color: #007bff;
  font-weight: bold;
}
</style>