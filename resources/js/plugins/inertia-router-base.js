// inertia-router-base.js
export default {
  install(app) {
    const BASE = '/ventanilla_unica/public/'

    function normalize(url) {
      if (url.startsWith(BASE)) return url
      if (!url.startsWith('/')) url = '/' + url
      return BASE + url
    }

    const methods = ['visit', 'get', 'post', 'put', 'patch', 'delete', 'reload', 'replace']

    app.mixin({
      beforeCreate() {
        // Solo una vez por instancia
        if (!this.$inertia || this._inertiaWrapped) return
        this._inertiaWrapped = true

        methods.forEach(method => {
          const original = this.$inertia[method]
          this.$inertia[method] = (...args) => {
            // Primer argumento es URL (string)
            args[0] = normalize(args[0])
            return original.apply(this.$inertia, args)
          }
        })
      }
    })
  },
}
