<template>
  <div>

    <Head :title="`${form.first_name} ${form.last_name}`" />
    <div class="flex items-center p-2 md:p-6 justify-between gap-4 flex-wrap">
      <!-- Izquierda: Botón "Nuevo" y título -->
      <div class="flex items-center gap-2">
        <Link class="text-primary hover:text-indigo-600 font-bold" href="/users">Users<span class="text-primary-400 font-medium">/</span>
        </Link><h1 class="text-sm font-bold">{{form.first_name}} {{ form.last_name }}</h1>
      </div>


      <img v-if="user.photo" class="block ml-4 w-8 h-8 rounded-full" :src="user.photo" />


      <!-- Centro: Filtro -->
      <div class="flex-1 flex justify-center">

      </div>

      <!-- Derecha: Botón placeholder -->
      <div>

      </div>
    </div>


    <trashed-message v-if="user.deleted_at" class="mb-6" @restore="restore"> This user has been deleted.
    </trashed-message>
    <div class="max-w-3xl  rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-4 -mr-6 p-4">
          <text-input v-model="form.first_name" :error="form.errors.first_name" class="pb-8 pr-6 w-full lg:w-1/2"
            label="First name" />
          <text-input v-model="form.last_name" :error="form.errors.last_name" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Last name" />
          <text-input v-model="form.email" :error="form.errors.email" class="pb-8 pr-6 w-full lg:w-1/2" label="Email" />
          <text-input v-model="form.password" :error="form.errors.password" class="pb-8 pr-6 w-full lg:w-1/2"
            type="password" autocomplete="new-password" label="Password" />
          <select-input v-model="form.owner" :error="form.errors.owner" class="pb-8 pr-6 w-full lg:w-1/2" label="Owner">
            <option :value="true">Yes</option>
            <option :value="false">No</option>
          </select-input>
          <file-input v-model="form.photo" :error="form.errors.photo" class="pb-8 pr-6 w-full lg:w-1/2" type="file"
            accept="image/*" label="Photo" />
          <!-- NUEVO: Roles -->
          <div class="pb-8 pr-6 w-full lg:w-1/2">
            <label class="block mb-2 font-semibold text-gray-700">Roles</label>
            <div v-for="role in roles" :key="role.id" class="mb-1">
              <label class="inline-flex items-center">
                <input type="checkbox" :value="role.name" v-model="form.roles" class="form-checkbox" />
                <span class="ml-2">{{ role.name }}</span>
              </label>
            </div>
            <p v-if="form.errors.roles" class="text-red-600 text-sm mt-1">{{ form.errors.roles }}</p>
          </div>
        </div>
        <div class="flex items-center px-8 py-4 border-t border-gray-100">
          <button v-if="!user.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button"
            @click="destroy">Delete User</button>
          <loading-button :loading="form.processing" class="btn btn-primary ml-auto" type="submit">Update
            User</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import FileInput from '@/Shared/FileInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import TrashedMessage from '@/Shared/TrashedMessage.vue'

export default {
  components: {
    FileInput,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    user: Object,
    roles: Array
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        _method: 'put',
        first_name: this.user.first_name,
        last_name: this.user.last_name,
        email: this.user.email,
        password: '',
        owner: this.user.owner,
        photo: null,
        roles: this.user.roles || [],  // roles actuales del usuario
      }),
    }
  },
  methods: {
    update() {
      this.form.post(`/users/${this.user.id}`, {
        onSuccess: () => this.form.reset('password', 'photo'),
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this user?')) {
        this.$inertia.delete(`/users/${this.user.id}`)
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this user?')) {
        this.$inertia.put(`/users/${this.user.id}/restore`)
      }
    },
  },
}
</script>
