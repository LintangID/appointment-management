<template lang="">
  <div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="card shadow-sm p-4" style="width: 100%; max-width: 400px">
      <h3 class="text-center mb-4">Login</h3>
      <form @submit.prevent>
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" v-model="username" class="form-control" id="username" placeholder="Masukan username" required />
        </div>
        <button @click="login()" class="btn btn-primary w-100 form-control mb-3">Login</button>
      </form>
    </div>
  </div>
</template>
<script>
import axios from 'axios';
import router from '@/router';

export default {
  data() {
    return {
      username: '',
    };
  },
  methods: {
    login() {
      axios
        .post('http://localhost:8000/api/auth/login', {
          username: this.username,
        })
        .then(function (response) {
          localStorage.setItem('name', response.data.data.name);
          localStorage.setItem('token', response.data.data.token);
          router.push({ name: 'home' });
        })
        .catch(function (error) {
          console.log(error.response.data.message);
          alert(error.response.data.message);
        });
    },
  },
  mounted() {
    this.user = localStorage.getItem('name');
    if (this.user != null) {
      router.push({ name: 'home' });
    }
  },
};
</script>
<style lang=""></style>
