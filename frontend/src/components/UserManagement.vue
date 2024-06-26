<template>
    <DashboardView />
    <div class="container">
        <div class="jumbotron">
            <button class="btn btn-info btn-sm" @click="addDoctor()">Add a User</button>
        </div>

        <div class="row">
            <div class="col">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>User Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>{{ user.id }}</td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.userType }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" @click="editUser(user)">Edit</button>
                                <button class="btn btn-danger btn-sm" @click="deleteUser(user)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Modal v-if="showAddUserModal" @close="showAddUserModal = false">
            <template v-slot:header>
                <h5>Add New User</h5>
            </template>
            <template v-slot:body>
                <form @submit.prevent="postUser()" class="registration-form">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input class="form-control" type="text" v-model="newUserData.name" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input class="form-control" type="text" v-model="newUserData.email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input class="form-control" type="password" v-model="newUserData.password" id="password" required>
                    </div>
                    <div class="form-group">
                        <label for="userType">User Type:</label>
                        <select class="form-control" id="userType" v-model="newUserData.userType" required>
                            <option value="admin">Administrator</option>
                            <option value="doctor">Doctor</option>
                            <option value="patient">Patient</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
                <div v-if="error" class="error">{{ error }}</div>
            </template>
        </Modal>

        <Modal v-if="showeditUserModal" @close="showeditUserModal = false">
            <template v-slot:header>
                <h5>Edit User Details</h5>
            </template>
            <template v-slot:body>
                <form @submit.prevent="updateUser()" class="registration-form">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input class="form-control" type="text" v-model="editUserData.name" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input class="form-control" type="text" v-model="editUserData.email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input class="form-control" type="password" v-model="editUserData.password" id="password" required>
                    </div>
                    <div class="form-group">
                        <label for="userType">User Type:</label>
                        <select class="form-control" id="userType" v-model="editUserData.userType" required>
                            <option value="admin">Administrator</option>
                            <option value="doctor">Doctor</option>
                            <option value="patient">Patient</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
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
            users: [], // Array to store users
            showAddUserModal: false, // Boolean to control visibility of Add User modal
            showeditUserModal: false, // Boolean to control visibility of Edit User modal
            newUserData: {
                name: '',
                email: '',
                password: '',
                userType: ''
            }, // Object to store new user data
            editUserData: {
                id: '',
                name: '',
                email: '',
                password: '',
                userType: '',
                updated_at: ''
            } // Object to store data of the user being edited
        };
    },
    mounted() {
        this.fetchAllUsers(); // Fetch all users when the component is mounted
    },
    methods: {
        // Fetch all users from the server
        fetchAllUsers() {
            fetch('http://127.0.0.1:8000/api/user', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
            })
                .then(response => response.json())
                .then(data => {
                    this.users = data.UserAccounts; // Set users data
                })
                .catch(err => {
                    if (err.response) {
                        this.error = `Error: ${err.response.data.message}`; // Handle response error
                        console.error(err.response.data);
                    } else if (err.request) {
                        this.error = 'No response from server. Please try again later.'; // Handle request error
                        console.error(err.request);
                    } else {
                        this.error = 'Request error. Please check your input and try again.'; // Handle general error
                        console.error('Error', err.message);
                    }
                });
        },
        // Show the Add User modal
        addDoctor() {
            this.showAddUserModal = true;
        },
        // Submit new user data to the server
        async postUser() {
            try {
                await axios.post('http://127.0.0.1:8000/api/user', this.newUserData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                this.showAddUserModal = false; // Close the Add User modal
            } catch (error) {
                console.error('There was an error adding the user:', error); // Handle post error
            }
            this.fetchAllUsers(); // Refresh users list
        },
        // Show the Edit User modal and set user data
        editUser(user) {
            this.editUserData = { ...user };
            this.showeditUserModal = true;
        },
        // Submit updated user data to the server
        async updateUser() {
            this.editUserData.updated_at = new Date().toISOString(); // Set updated_at field
            try {
                await axios.put(`http://127.0.0.1:8000/api/user/${this.editUserData.id}`, this.editUserData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
                this.showeditUserModal = false; // Close the Edit User modal
                this.fetchAllUsers(); // Refresh users list
            } catch (error) {
                console.error('There was an error updating the user:', error); // Handle update error
            }
        },
        // Delete a user
        async deleteUser(user) {
            const index = this.users.findIndex(u => u.id === user.id);
            if (index !== -1) {
                this.users.splice(index, 1); // Optimistically remove user from the list
            }
            try {
                await axios.delete(`http://127.0.0.1:8000/api/user/${user.id}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                });
            } catch (error) {
                console.error('There was an error deleting the user:', error); // Handle delete error
            }
        }
    }
};
</script>
