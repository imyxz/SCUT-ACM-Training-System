import Materialize from 'materialize-css/dist/js/materialize.min.js'
// This is learn from http://blog.csdn.net/magneto7/article/details/70773954
export default {
  install: function (Vue) {
    Object.defineProperty(Vue.prototype, '$Materialize', { value: Materialize })
  }
}
