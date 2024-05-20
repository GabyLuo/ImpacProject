// TODO - Quitar vanilla
export default {
  draggable (elm) {
    let element = elm
    let isMouseDown = false
    let mouseX
    let mouseY
    let elementX = 0
    let elementY = 0
    element.style.transition = 'none'
    let headerElement = element.querySelector('.q-layout-header')
    headerElement.addEventListener('mousedown', (event) => {
      mouseX = event.clientX
      mouseY = event.clientY
      isMouseDown = true
    })
    headerElement.addEventListener('mouseup', (event) => {
      isMouseDown = false
      elementX = parseInt(element.style.left) || 0
      elementY = parseInt(element.style.top) || 0
    })
    document.addEventListener('mousemove', (event) => {
      if (!isMouseDown) return
      let deltaX = event.clientX - mouseX
      let deltaY = event.clientY - mouseY
      element.style.left = elementX + deltaX + 'px'
      element.style.top = elementY + deltaY + 'px'
    })
  },
  destroyListeners (elm) {
    let element = elm
    element.removeEventListener('mousedown')
    element.removeEventListener('mouseup')
    document.removeEventListener('mousemove')
  },
  transition (show, selector, modal) {
    let element = document.querySelector(selector)
    if (show) {
      modal.transition = element.style.transition
      modal.x = element.style.left
      modal.y = element.style.top
      this.draggable(element)
    } else {
      element.style.transition = modal.transition
      setTimeout(() => {
        element.style.left = modal.x
        element.style.top = modal.y
      }, 500)
    }
  }
}
