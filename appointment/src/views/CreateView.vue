<template lang="">
  <!-- navbar component -->
  <NavBar :name="user" />
  <div class="container mt-4 mb-5">
    <div class="row justify-content-center">
      <div class="col-md-7">
        <div class="card rounded shadow">
          <div class="card-header">Add Appointment</div>
          <div class="card-body">
            <form @submit.prevent>
              <div class="form-group mb-2">
                <label class="form-label" for="title">Title</label>
                <input type="text" v-model="title" id="title" class="form-control form-control-sm" placeholder="title" required />
              </div>
              <div class="form-group mb-2">
                <label class="form-label" for="participant">Participant</label>
                <select v-model="participant_id" id="participant" class="form-select form-select-sm" required>
                  <option disabled value="">Participant</option>
                  <option v-for="participant in participants" :value="participant.id">{{ participant.name }}</option>
                </select>
              </div>
              <div class="form-group mb-2">
                <label class="form-label" for="start">Start</label>
                <input type="time" v-model="start" id="start" class="form-control form-control-sm" required />
              </div>
              <div class="form-group mb-3">
                <label class="form-label" for="end">End</label>
                <input type="time" v-model="end" id="end" class="form-control form-control-sm" required />
              </div>

              <button @click="submitAppointment()" class="btn btn-primary">SAVE</button>
            </form>
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
      participants: [],
      title: '',
      participant_id: '',
      start: '',
      end: '',
    };
  },
  mounted() {
    this.user = localStorage.getItem('name');
    if (!this.user || this.user == '' || this.user == null) {
      router.push({ name: 'login' });
    }
    this.getParticipants();
  },
  methods: {
    getParticipants() {
      axios
        .get('http://localhost:8000/api/users', {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
        })
        .then((response) => {
          this.participants = response.data;
        })
        .catch(function (error) {
          console.log(error.response.data.message);
        });
    },
    submitAppointment() {
      let payload = {
        title: this.title,
        participant_id: this.participant_id,
        start: this.start,
        end: this.end,
      };
      axios
        .post('http://localhost:8000/api/appointments', payload, {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
        })
        .then((response) => {
          console.log(response.data.message);
          router.push({ name: 'home' });
        })
        .catch(function (error) {
          console.log(error.response.data.message);
          alert(error.response.data.message);
        });
    },
  },
};
</script>
<style lang=""></style>
