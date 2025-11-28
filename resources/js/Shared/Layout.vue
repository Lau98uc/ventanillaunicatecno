<template>
  <div :data-theme="currentTheme" class="flex flex-col min-h-screen">
    <!-- Menú para móviles (placeholder para teleport de dropdown) -->
    <div id="dropdown" />

    <div class="md:flex md:flex-col flex-1">
      <!-- Header SOLO visible en móvil -->
      <div class="flex items-center justify-between px-4 py-2 bg-primary text-primary-content md:hidden">
        <!-- Logo + Menú hamburguesa -->
        <div class="flex items-center gap-2">
          <Link href="/">
          <logo class="fill-current" width="100" height="24" />
          </Link>

          <!-- Dropdown del menú -->
          <dropdown placement="bottom-start">
            <template #default>
              <button class="btn btn-sm btn-ghost text-primary-content">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              </button>
            </template>
            <template #dropdown>
              <div class="mt-2 p-4 bg-primary-focus rounded shadow-lg">
                <main-menu :menuItems="auth.menuItems" />
              </div>
            </template>
          </dropdown>
        </div>

        <!-- Selector de tema + usuario -->
        <div class="flex items-center gap-2">
          <!-- Tema -->
          <select v-model="perfil" @change="actualizarTema" class="select select-sm">
            <option disabled value="">Perfil</option>
            <option>niño</option>
            <option>joven</option>
            <option>adulto</option>
          </select>

          <select v-model="modo" @change="actualizarTema" class="select select-sm">
            <option disabled value="">Modo</option>
            <option value="claro">Claro</option>
            <option value="oscuro">Oscuro</option>
            <option value="automatico">Automático</option>
          </select>

          <!-- Usuario -->
          <dropdown placement="bottom-end">
            <template #default>
              <div class="flex items-center cursor-pointer select-none text-xs">
                <span>{{ auth.user.first_name }}</span>
                <icon name="cheveron-down" class="w-4 h-4 ml-1" />
              </div>
            </template>
            <template #dropdown>
              <div class="mt-2 py-1 bg-base-100 rounded shadow-xl text-base-content">
                <Link class="block px-4 py-2 hover:bg-base-200" :href="`/users/${auth.user.id}/edit`">Perfil</Link>
                <Link class="block px-4 py-2 hover:bg-base-200" href="/users">Usuarios</Link>
                <Link class="block px-4 py-2 hover:bg-base-200" href="/logout" method="delete" as="button">Salir</Link>
              </div>
            </template>
          </dropdown>
        </div>
      </div>

      <!-- Header para md y superior -->
      <div class="md:flex md:shrink-0 hidden">
        <div class="flex items-center justify-between px-6 py-2 bg-primary md:shrink-0 md:justify-center md:w-56">
          <Link class="mt-1" href="/">
          <logo class="fill-current text-base-content" width="120" height="28" />
          </Link>
        </div>

        <div
          class="md:text-md flex items-center justify-between p-2 w-full text-sm bg-base-100 border-b md:px-4 md:py-0 text-base-content">
          <!-- <div class="mr-4 mt-1">{{ auth.user.account.name }}</div> -->

          <!-- Izquierda: Botón "Nuevo" y título -->
          <div class="flex items-center gap-4 flex-wrap">

            <ul class="menu menu-horizontal p-0">
              <li v-for="item in sidebarItems" :key="item.link">
                <Link :href="item.link">{{ item.title }}</Link>
              </li>
            </ul>


          </div>


          <select v-model="perfil" @change="actualizarTema" class="select select-sm select-bordered w-35 ml-auto mr-2">
            <option disabled value="">Perfil</option>
            <option value="ninho">niño</option>
            <option value="joven">joven</option>
            <option value="adulto">adulto</option>
          </select>


          <select v-model="modo" @change="actualizarTema" class="select select-sm w-30 mr-2">
            <option disabled value="">Modo</option>
            <option value="claro">Claro</option>
            <option value="oscuro">Oscuro</option>
            <option value="automatico">Automático</option>
          </select>

          <Link class=" group mt-1 flex items-center gap-1 me-4" href="/notifications" aria-label="Notificaciones">
          <!-- Ícono campana -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5  group-hover:text-primary" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          </Link>

          <dropdown class="mt-1" placement="bottom-end">
            <template #default>
              <div class="group flex items-center cursor-pointer select-none">
                <div class="mr-1 text-base-content group-hover:text-primary whitespace-nowrap text-sm">
                  <span>{{ auth.user.first_name }}</span>
                  <span class="hidden md:inline">&nbsp;{{ auth.user.last_name }}</span>
                </div>
                <icon class="w-4 h-4 fill-current text-base-content group-hover:text-primary" name="cheveron-down" />
              </div>
            </template>
            <template #dropdown>
              <div class="mt-2 py-1 text-sm bg-base-100 rounded shadow-xl">
                <Link class="block px-6 py-2 hover:text-white hover:bg-primary" :href="`/users/${auth.user.id}/edit`">My
                Profile</Link>
                <Link class="block px-6 py-2 hover:text-white hover:bg-primary" href="/users">Manage Users</Link>
                <Link class="block px-6 py-2 w-full text-left hover:text-white hover:bg-primary" href="/logout"
                  method="delete" as="button">Logout</Link>
              </div>
            </template>
          </dropdown>
        </div>
      </div>

      <!-- Contenedor principal -->
      <div class="md:flex md:grow md:overflow-hidden flex-1 texto-arcoiris">
        <main-menu :menuItems="auth.menuItems"
          class="hidden shrink-0 p-6 w-56 bg-primary-focus overflow-y-auto md:block" />


        <div class="md:flex-1 md:overflow-y-auto flex flex-col">
          <flash-messages />

          <div class="flex-1 px-2">
            <slot />
          </div>

          <footer class="bg-base-200 text-sm text-base-content px-6 py-4 border-t">
            <div class="flex flex-col md:flex-row justify-between items-center gap-2 w-full">
              <div>&copy; {{ new Date().getFullYear() }} Your Company</div>
              <div class="flex gap-4 items-center">
                <a href="#" class="hover:underline">Privacy</a>
                <a href="#" class="hover:underline">Terms</a>
                <a href="#" class="hover:underline">Support</a>
                <span class="ml-6 flex items-center">
                  <span class="text-base font-bold text-green-900 mr-2">Visitas a esta página:</span>
                  <span class="bg-success text-success-content rounded-full px-4 py-1 text-lg font-bold shadow border-2 border-success">
                    {{ $page.props.visitasPaginaActual || 0 }}
                  </span>
                </span>
              </div>
            </div>
          </footer>
        </div>
      </div>
    </div>
  </div>
</template>



<script>
import { Link } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import Logo from '@/Shared/Logo.vue'
import Dropdown from '@/Shared/Dropdown.vue'
import MainMenu from '@/Shared/MainMenu.vue'
import FlashMessages from '@/Shared/FlashMessages.vue'

export default {
  components: {
    Dropdown,
    FlashMessages,
    Icon,
    Link,
    Logo,
    MainMenu,
  },
  props: {
    auth: Object,
  },
  data() {
    return {
      currentTheme: 'pingcrm',
      themes: [
        /* "light", "dark", "cupcake", "bumblebee", "emerald",
        "corporate", "synthwave", "retro", "cyberpunk", "valentine",
        "halloween", "garden", "forest", "aqua", "lofi", "pastel", "fantasy",
        "wireframe", "black", "luxury", "dracula", "cmyk", "autumn", "business",
        "acid", "lemonade", "night", "coffee", "winter", "ciberpunkdark" */
        'niño', 'joven', 'adulto'
      ],
      sidebarItems: [],
      unreadCount: 3,
      themeMatrix: {
        ninho: {
          claro: 'bumblebee',
          oscuro: 'halloween',
          automatico: 'auto-niño'  // lo resolvemos en tiempo real
        },
        joven: {
          claro: 'emerald',
          oscuro: 'forest',
          automatico: 'auto-joven'
        },
        adulto: {
          claro: 'light',
          oscuro: 'dark',
          automatico: 'auto-adulto'
        }
      },
      perfil: 'joven',
      modo: 'claro',
    }
  },
  created() {
    // Carga el tema guardado en localStorage o pone 'pingcrm' por defecto
    const savedTheme = localStorage.getItem('theme')
    if (savedTheme) {
      this.currentTheme = savedTheme
    }

    this.sidebarItems = this.auth?.menuItems || [];
  },
  watch: {
    // Cada vez que el prop auth cambie, actualiza sidebarItems
    auth: {
      handler(newAuth) {
        this.sidebarItems = newAuth?.menuItems || []
      },
      deep: true
    }
  },

  methods: {
    saveTheme() {
      localStorage.setItem('theme', this.currentTheme)
    },

    actualizarTema() {
      let perfil = this.perfil
      let modo = this.modo
      console.log(perfil, modo);

      if (!perfil || !modo) return

      let tema = this.themeMatrix[perfil][modo]

      if (modo === 'automatico') {
        const horas = new Date().getHours()
        tema = horas >= 18 || horas < 6
          ? this.themeMatrix[perfil]['oscuro']
          : this.themeMatrix[perfil]['claro']
      }

      this.currentTheme = tema
      localStorage.setItem('perfil', perfil)
      localStorage.setItem('modo', modo)
      localStorage.setItem('theme', tema)
    },


    capitalize(str) {
      return str.charAt(0).toUpperCase() + str.slice(1)
    },
    toggleNotifications() {
      // Aquí abres un modal o sidebar con notificaciones
      console.log('Abrir panel de notificaciones');
    }
  },
  mounted() {
    console.log(this.auth.menuItems)
  }
}
</script>
