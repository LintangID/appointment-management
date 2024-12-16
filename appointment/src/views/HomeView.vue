<template lang="">
  <!-- navbar component -->
  <NavBar :name="user" />
  <div class="container mt-5 mb-5">
    <div class="row">
      <div class="col-md-12">
        <div class="card rounded shadow">
          <div class="card-header">Data Appointment</div>
          <div class="card-body">
            <router-link :to="{ name: 'create' }" class="btn btn-md btn-success rounded shadow border-0 mb-3">ADD NEW APPOINTMENT</router-link>
            <table class="table table-bordered table-striped table-hover">
              <thead class="bg-dark text-white">
                <tr>
                  <th scope="col">Title</th>
                  <th scope="col">Participant</th>
                  <th scope="col">Start</th>
                  <th scope="col">End</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="appointments.length == 0">
                  <td colspan="4" class="text-center">
                    <div class="alert alert-danger mb-0">Data Belum Tersedia!</div>
                  </td>
                </tr>
                <tr v-else v-for="appointment in appointments">
                  <td>{{ appointment.title }}</td>
                  <td>{{ appointment.participants[0].name }}</td>
                  <td>{{ appointment.start }}</td>
                  <td>{{ appointment.end }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios';
import router from '@/router';
import NavBar from '@/components/NavBar.vue';
export default {
  components: {
    NavBar,
  },
  data() {
    return {
      user: '',
      appointments: [],
    };
  },
  mounted() {
    this.user = localStorage.getItem('name');
    if (!this.user || this.user == '' || this.user == null) {
      router.push({ name: 'login' });
    }
    this.getAppointments();
  },
  methods: {
    getAppointments() {
      axios
        .get('http://localhost:8000/api/appointments', {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
        })
        .then((response) => {
          this.appointments = response.data.data;
        })
        .catch(function (error) {
          console.log(error);
        });
    },
  },
};
</script>
<style lang=""></style>
