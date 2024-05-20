const TOKEN = 'jwtToken'

export default {
  setToken (token) {
    localStorage.setItem(TOKEN, token)
  },
  checkToken () {
    return localStorage.getItem(TOKEN)
  },
  destroyToken () {
    localStorage.removeItem(TOKEN)
  }
}
