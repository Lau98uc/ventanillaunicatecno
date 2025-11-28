<template>
  <div>

    <Head title="Crear Rol" />

    <div class="flex items-center p-2 md:p-4 justify-between gap-4 flex-wrap">
      <div class="flex items-center gap-2">
        <Link class="text-indigo-400 hover:text-indigo-600" href="/roles">
        Roles
        <span class="text-indigo-400 font-medium">/</span> Crear
        </Link>
      </div>
    </div>

    <div class="max-w-3xl rounded-md shadow overflow-hidden">
      <form @submit.prevent="store" class="p-8">
        <div class="flex flex-wrap -mb-8 -mr-6">
          <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Nombre del rol" />

          <div class="pb-8 pr-6 w-full">
            <label class="block mb-2 font-medium">Permisos agrupados</label>

            <div class="max-h-[400px] overflow-y-auto border p-4 rounded space-y-4">
              <div v-for="modulo in permisos" :key="modulo.id" class="mb-4">
                <h2 class="text-lg font-bold mb-2 text-indigo-600">{{ modulo.label || modulo.name }}</h2>

                <div v-for="menu in modulo.children" :key="menu.id" class="ml-4 mb-2">
                  <div class="flex items-center justify-between">
                    <h3 class="font-semibold">{{ menu.label || menu.name }}</h3>
                    <label class="inline-flex items-center text-sm cursor-pointer">
                      <input type="checkbox" class="checkbox checkbox-xs checkbox-secondary"
                        :checked="isMenuFullyChecked(menu)" @change="toggleMenuPermissions(menu)" />
                      <span class="ml-2">Todos</span>
                    </label>
                  </div>

                  <div v-for="perm in menu.children" :key="perm.id" class="ml-4">
                    <label class="inline-flex items-center space-x-2">
                      <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" :value="perm.id"
                        v-model="form.permissions" />
                      <span>{{ perm.label || perm.name }}</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <p v-if="form.errors.permissions" class="text-red-600 text-sm mt-1">{{ form.errors.permissions }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end px-8 py-4 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn btn-primary" type="submit">
            Crear Rol
          </loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'

export default {
  components: {
    Head,
    Link,
    TextInput,
    LoadingButton,
  },
  layout: Layout,
  props: {
    permisos: Array, // módulos > modelos > permisos
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: '',
        permissions: [],
      }),
    }
  },
  methods: {
    store() {
      this.form.post('/roles')
    },
    isMenuFullyChecked(menu) {
      return menu.children.every(p => this.form.permissions.includes(p.id))
    },
    toggleMenuPermissions(menu) {
      const allIds = menu.children.map(p => p.id)
      const isAllChecked = this.isMenuFullyChecked(menu)

      if (isAllChecked) {
        this.form.permissions = this.form.permissions.filter(id => !allIds.includes(id))
      } else {
        this.form.permissions = Array.from(new Set([...this.form.permissions, ...allIds]))
      }
    }
  },
}
</script>
