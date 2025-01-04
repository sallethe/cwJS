const HIGHLIGHT_CLASS_NAME = 'highlighted'

/*** @type {Grid | EditableGrid} */
let overseer = null

/***
 @param {string} tag
 @returns HTMLElement
 */
const getFromId = (tag) =>
    document.getElementById(tag)

/***
 @param {number} x
 @param {number} y
 @returns {{x: number, y: number}}
 */
const c = (x, y) => ({
    x: x,
    y: y,
})

/***
 @prop {Grid} grid
 @prop {string[][]} answers
 @prop {number} id
 */
class Solver {

    grid
    answers
    id

    /***
     @param {Grid} grid
     @param {string[][]} answers
     @param {number} id
     */
    constructor(grid, answers, id) {
        if (!this._preliminaryChecks(grid, answers))
            throw new Error(`Incompatible grid.`)
        this.grid = grid
        this.answers = answers
        this.id = id
        getFromId('check').onclick =
            () => this.check()
    }

    /***
     @param {Grid} grid
     @param {string[][]} answers
     @returns boolean
     */
    _preliminaryChecks(grid, answers) {
        if (answers.length !== grid.dim) return false
        for (let i of answers)
            if (i.length !== grid.dim) return false
        return true
    }

    /***
     @returns void
     */
    check() {
        let wrongs = 0
        for (let x = 0; x < this.answers.length; x += 1) {
            for (let y = 0; y < this.answers[x].length; y += 1) {
                const cell = this.grid.matrix[x][y]
                if (!cell.disabled && cell.getValue() === this.answers[x][y])
                    cell.highlight()
                else
                    wrongs += 1
            }
        }
        if (wrongs === 0) this._victory()
    }

    /***
     @returns void
     */
    _victory() {
        const a = document.createElement('a')
        a.href = `/cwJS/congrats/?id=${this.id}`
        a.click()
    }
}

/***
 @prop {{x: number,y: number}} coords
 @prop {HTMLInputElement} element
 @prop {boolean} isDisabled
 */
class Cell {

    coords
    element
    isDisabled

    /***
     @param {{x: number,y: number}} coords
     @param {boolean} state
     */
    constructor(coords, state = false) {
        if (coords.x < 0 || coords.y < 0)
            throw new Error('Invalid coordinates.')
        this.coords = coords
        this.isDisabled = state
        this._createElement()
    }

    /***
     @returns void
     */
    _createElement() {
        const e = document.createElement('input')
        e.maxLength = 1
        e.classList.add('Cell')

        e.onchange = () => e.value =
            /^[A-Za-z]$/.test(e.value) ?
                e.value.toUpperCase()
                : ''
        e.onfocus = () => {
            overseer.unselect()
            overseer.selectCell(this.coords)
        }
        e.onblur = () => overseer.unselect()

        if (this.isDisabled) {
            e.classList.add('disabled')
            e.readOnly = true
            e.disabled = true
        }
        this.element = e
    }

    /***
     @returns void
     */
    highlight() {
        if (!this.isDisabled)
            this.element.classList.add(HIGHLIGHT_CLASS_NAME)
    }

    /***
     @returns void
     */
    blur() {
        if (!this.isDisabled)
            this.element.classList.remove(HIGHLIGHT_CLASS_NAME)
    }

    /***
     @returns string
     */
    getValue() {
        return this.element.value
    }
}

/***
 @param {boolean} e1
 @param {boolean} e2
 @returns boolean
 */
const xor = (e1, e2) => e1 !== e2

/***
 @prop {Cell[][]} matrix
 @prop {number} dim
 */
class Grid {
    matrix
    dim

    /***
     @param {string[][]} values
     @param {boolean} containsDisabled
     */
    constructor(values, containsDisabled = true) {
        const mtx = []
        const dim = values.length
        for (let x = 0; x < dim; x += 1) {
            const l = []
            for (let y = 0; y < dim; y += 1)
                l.push(new Cell(c(x, y),
                    (values[x][y] === '' && containsDisabled)))
            mtx.push(l)
        }
        this.matrix = mtx
        this.dim = dim
        overseer = this
    }

    /***
     @returns void
     */
    generate() {
        const grid = getFromId('grid')
        if (!grid) return
        grid.innerText = ''
        for (const l of this.matrix) {
            const row = document.createElement('div')
            for (const e of l) row.append(e.element)
            grid.appendChild(row)
        }
    }

    selectCell(coords) {
        if (coords.x < 0 || coords.y < 0) return
        for (let x = 0; x < this.dim; x += 1)
            for (let y = 0; y < this.dim; y += 1)
                if (xor(x === coords.x, y === coords.y))
                    this.matrix[x][y].highlight()
    }

    /***
     @param {boolean} vertical
     @param {number} x
     @param {number} y
     @param {{x: number, y: number}} start
     @param {number} length
     @returns boolean
     */
    _isSelectable(vertical, x, y, start, length) {
        return (
            (
                vertical
                && y === start.y
                && x >= start.x
                && x <= start.x + length
            ) || (
                !vertical
                && x === start.x
                && y >= start.y
                && x <= start.y + length
            )
        )
    }

    /***
     @returns void
     */
    selectRow(start, vertical, length) {
        if (start.x < 0 || start.y < 0 || length < 0) return
        for (let x = 0; x < this.dim; x += 1)
            for (let y = 0; y < this.dim; y += 1)
                if (this._isSelectable(vertical, x, y, start, length))
                    this.matrix[x][y].highlight()
    }

    /***
     @returns void
     */
    unselect() {
        for (const row of this.matrix)
            for (const elem of row)
                elem.blur()
    }
}

/***
 @prop {boolean} vertical
 @prop {{x: number, y:number}} coords
 @prop {number} length
 @prop {string} definition
 @prop {HTMLDivElement} element
 */
class Word {
    vertical
    coords
    length
    definition
    element

    /***
     @param {boolean} vertical
     @param {{x: number, y:number}} coords
     @param {number} length
     @param {string} definition
     */
    constructor(vertical, coords, length, definition) {
        this.vertical = vertical
        if (coords.x < 0 || coords.y < 0)
            throw new Error('Invalid coordinates')
        this.coords = coords
        this.length = length
        this.definition = definition
        this._createElement()
    }

    /***
     @returns void
     */
    _createElement() {
        const entry = document.createElement('div')
        const title = document.createElement('h3')
        const def = document.createElement('p')
        title.innerHTML = `${this.vertical ?
            `Colonne ${this.coords.y + 1}`
            : `Ligne ${this.coords.x + 1}`} &bull; longueur ${this.length}`
        def.innerHTML = this.definition
        entry.appendChild(title)
        entry.appendChild(def)
        entry.onclick = () => {
            overseer.unselect()
            overseer.selectRow(
                this.coords, this.vertical, this.length,
            )
        }
        entry.onblur = () => overseer.unselect()
        this.element = entry
    }
}


/***
 @prop {Word[]} horizontal
 @prop {Word[]} vertical
 */
class WordSet {
    horizontal = []
    vertical = []

    /***
     @param {Word[]} words
     */
    constructor(words) {
        for (let word of words)
            if (word.vertical)
                this.vertical.push(word)
            else
                this.horizontal.push(word)
    }

    /***
     @returns void
     */
    generate() {
        const horDiv = getFromId('horizontal')
        for (let entry of this.horizontal) {
            const w = new Word(
                false,
                entry.coords,
                entry.length,
                entry.definition,
            )
            horDiv.appendChild(w.element)
        }
        const verDiv = getFromId('vertical')
        for (let entry of this.vertical) {
            const w = new Word(
                false,
                entry.coords,
                entry.length,
                entry.definition,
            )
            verDiv.appendChild(w.element)
        }
    }
}