const requireModule = require.context('.', false, /\.js$/)
const modules = {}

requireModule.keys().forEach(fileName => {
  if (fileName === './index.js') return
  const moduleName = fileName.replace(/(\.\/|\.js)/g, '')
  modules[moduleName] = requireModule(fileName).default
})

const module = {
  namespaced: true,
  modules
}

export default module
