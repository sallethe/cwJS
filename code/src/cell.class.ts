import {Coordinates} from "./commons";
import type {Grid} from "./grid.class";

const HIGHLIGHT_CLASS_NAME = 'selected'

export class Cell {
    coords: Coordinates
    element: HTMLInputElement
    isDisabled: boolean
    grid: Grid

    constructor(coords: Coordinates, grid: Grid, state = false) {
        if (coords.x < 0 || coords.y < 0) throw new Error('Invalid coordinates.')
        this.coords = coords
        this.grid = grid
        this.isDisabled = state
        const e = document.createElement('input')
        e.maxLength = 1
        e.classList.add('Cell')
        e.onchange = () => (e.value = e.value.toUpperCase())
        e.onfocus = () => this.grid.selectCell(this.coords)
        e.onblur = () => this.grid.unselect()
        if (state) {
            e.classList.add('disabled')
            e.readOnly = true
            e.disabled = true
        }
        this.element = e
    }

    highlight() {
        if(!this.isDisabled)
            this.element.classList.add(HIGHLIGHT_CLASS_NAME)
    }

    blur() {
        if(!this.isDisabled)
            this.element.classList.remove(HIGHLIGHT_CLASS_NAME)
    }

    getValue() {
        return this.element.value
    }
}
