class EditableGrid extends Grid {

    /***
     @param {string[][]} initializer
     */
    constructor(initializer) {
        super(initializer, false)
        for (let x = 0; x < this.dim; x += 1)
            for (let y = 0; y < this.dim; y += 1)
                this.matrix[x][y].element.value = initializer[x][y]
    }

    /***
     @param {number} dim
     @returns void
     */
    resizeGrid(dim) {
        if (dim < 1) return
        if (dim === this.dim) return

        if (dim < this.dim) {
            while (this.matrix.length > dim)
                this.matrix.pop()
            for (let x = 0; x < dim; x += 1)
                while (this.matrix[x].length > dim)
                    this.matrix[x].pop()
        }

        if (dim > this.dim) {
            for (let x = 0; x < this.dim; x += 1)
                for (let y = this.dim; y < dim; y += 1)
                    this.matrix[x].push(new Cell(c(x, y)))
            for (let x = this.dim; x < dim; x += 1) {
                const l = []
                for (let y = 0; y < dim; y += 1)
                    l.push(new Cell(c(x, y)))
                this.matrix.push(l)
            }
        }
        this.dim = dim
        this.generate()
        this.unselect()
    }

    /***
     @returns string[][]
     */
    convertIntoAnswer() {
        const res = []
        for (let x of this.matrix) {
            const l = []
            for (let y of x) l.push(y.getValue())
            res.push(l)
        }
        return res
    }
}

/***
 @prop {HTMLDivElement} horList
 @prop {HTMLDivElement} verList
 */
class EditableWordSet {
    horList
    verList

    /***
     @param {Partial<Word>[]} initializer
     */
    constructor(initializer = []) {
        this.horList = getFromId('horizontal')
        this.verList = getFromId('vertical')

        const value =
            getFromId('dimensions')

        getFromId('plus').onclick = () => {
            overseer.resizeGrid(overseer.dim + 1)
            value.innerText = String(overseer.dim)
        }
        getFromId('minus').onclick = () => {
            overseer.resizeGrid(overseer.dim - 1)
            value.innerText = String(overseer.dim)
        }

        value.innerText = String(overseer.dim)
        this.verList.getElementsByTagName('button')[0].onclick =
            () => this.createVerticalEntry()
        this.verList.getElementsByTagName('button')[1].onclick =
            () => this.removeVerticalEntry()
        this.horList.getElementsByTagName('button')[0].onclick =
            () => this.createHorizontalEntry()
        this.horList.getElementsByTagName('button')[1].onclick =
            () => this.removeHorizontalEntry()
        overseer.words = this

        if (initializer) this._initialize(initializer)
    }

    /***
     @param {Partial<Word>[]} initializer
     */
    _initialize(initializer) {
        for (let word of initializer) {
            if (word.vertical) {
                this.createVerticalEntry(
                    word.length,
                    word.coords.x,
                    word.coords.y,
                    word.definition,
                )
            } else {
                this.createHorizontalEntry(
                    word.length,
                    word.coords.x,
                    word.coords.y,
                    word.definition,
                )
            }
        }
    }

    /***
     @param {number} defLen
     @param {number} defX
     @param {number} defY
     @param {string} defDef
     @returns HTMLDivElement
     */
    _createEntry(defLen = 0,
                 defX = 0,
                 defY = 0,
                 defDef = 'Lorem Ipsum') {
        const e = document.createElement('div')
        e.classList.add('WordEntry')

        const len = document.createElement('input')
        len.type = 'number'
        len.placeholder = 'Longueur'
        len.value = String(defLen)
        const x = document.createElement('input')
        x.type = 'number'
        x.placeholder = 'X'
        x.min = '0'
        x.value = String(defX)
        const y = document.createElement('input')
        y.type = 'number'
        y.placeholder = 'Y'
        y.min = '0'
        y.value = String(defY)
        const def = document.createElement('input')
        def.type = 'text'
        def.placeholder = 'Définition'
        def.value = String(defDef)

        e.appendChild(len)
        e.appendChild(x)
        e.appendChild(y)
        e.appendChild(def)

        return e
    }

    /***
     @returns void
     */
    createVerticalEntry(
        dlen = 0,
        dx = 0,
        dy = 0,
        ddef = 'Lorem Ipsum',
    ) {
        const e = this._createEntry(dlen, dx, dy, ddef)
        this.verList.insertBefore(e,
            this.verList.querySelector('.ButtonsSet'))
    }

    /***
     @returns void
     */
    createHorizontalEntry(
        dlen = 0,
        dx = 0,
        dy = 0,
        ddef = 'Lorem Ipsum',
    ) {
        const e = this._createEntry(dlen, dx, dy, ddef)
        this.horList.insertBefore(e,
            this.horList.querySelector('.ButtonsSet'))
    }

    /***
     @returns void
     */
    removeHorizontalEntry() {
        const children = this.horList.children
        const len = children.length
        if (len > 1) children[len - 2].remove()
    }

    /***
     @returns void
     */
    removeVerticalEntry() {
        const children = this.verList.children
        const len = children.length
        if (len > 1) children[len - 2].remove()
    }

    /***
     @param {HTMLDivElement} entry
     @param {boolean} isVertical
     @returns Partial<Word>
     */
    _entryToWord(entry, isVertical = true) {
        if (entry.children.length === 4) {
            const len = entry.children[0]
            const x = entry.children[1]
            const y = entry.children[2]
            const def = entry.children[3]
            return {
                vertical: isVertical,
                coords: c(Number(x.value), Number(y.value)),
                length: Number(len.value),
                definition: def.value,
            }
        }
        return undefined
    }

    /***
     @returns Partial<Word>[]
     */
    convertIntoList() {
        const res = []
        for (const entry of this.verList.children) {
            const word = this._entryToWord(entry, true)
            if (word) res.push(word)
        }
        for (const entry of this.horList.children) {
            const word = this._entryToWord(entry, false)
            if (word) res.push(word)
        }
        return res
    }
}

/***
 @prop {EditableWordSet} words
 @prop {EditableGrid} grid
 @prop {HTMLParagraphElement} error
 */
class Verificator {
    words
    grid
    error

    /***
     @param {EditableWordSet} words
     @param {EditableGrid} grid
     */
    constructor(words, grid) {
        getFromId('checker').onclick =
            () => this.validate()

        this.error = getFromId('error')
        this.error.style.display = 'none'

        this.words = words
        this.grid = grid
    }

    /***
     @returns void
     */
    validate() {

        if (this._checkIfValid()) {
            const words = this.words.convertIntoList()
            const grid = this.grid.convertIntoAnswer()

            getFromId('wordse').value = JSON.stringify(words)
            getFromId('gride').value = JSON.stringify(grid)
            getFromId('title').value = getFromId('titleInput').value
            getFromId('diff').value = getFromId('diffInput').value
            getFromId('dim').value = String(grid.length)
            getFromId('count').value = String(words.length)
            getFromId('submitter').submit()
        }
    }

    /***
     @param {string} error
     @returns void
     */
    _displayError(error = 'Erreur inconnue.') {
        this.error.style.display = 'block'
        this.error.innerText = error
    }

    /***
     @param {Word} word
     @returns boolean
     */
    _checkWordUseful(word) {
        if (word.length <= 0) {
            this._displayError(
                `Le mot aux coord° (${word.coords.x}, ${word.coords.y})
                     est vide`)
            return false
        }

        if (word.coords.x < 0 || word.coords.y < 0) {
            this._displayError(
                `Les coords° (${word.coords.x}, ${word.coords.y}) sont 
                invalides`)
            return false
        }

        if (!word.definition) {
            this._displayError(
                `La définition du mot (${word.coords.x}, 
                    ${word.coords.y}) est vide`)
            return false
        }
        return true
    }

    /***
     @returns boolean
     */
    _checkIfValid() {
        const grid = this.grid.convertIntoAnswer()
        const words = this.words.convertIntoList()
        const dim = grid.length

        if (words.length === 0) {
            this._displayError('Mots manquants')
            return false
        }

        const dummyAnswer = []
        for (let i = 0; i < dim; i++) {
            const l = []
            for (let j = 0; j < dim; j++) l.push(false)
            dummyAnswer.push(l)
        }

        for (let word of words) {
            if (!this._checkWordUseful(word))
                return false
            if (!word.vertical) {
                if ((word.coords.y + word.length) > dim) {
                    this._displayError(
                        `Le mot (${word.coords.x}, ${word.coords.y}) 
                        dépasse la grille (${word.coords.y + word.length} > 
                        ${dim})`)
                    return false
                }
                for (let y = word.coords.y;
                     y < word.coords.y + word.length; y += 1)
                    dummyAnswer[word.coords.x][y] = true
            } else {
                if ((word.coords.x + word.length) > dim) {
                    this._displayError(
                        `Le mot (${word.coords.x}, ${word.coords.y}) 
                        dépasse la grille (${word.coords.x + word.length} > 
                        ${dim})`)
                    return false
                }
                for (let x = word.coords.x;
                     x < word.coords.x + word.length; x += 1)
                    dummyAnswer[x][word.coords.y] = true
            }
        }

        for (let x = 0; x < dim; x += 1) {
            for (let y = 0; y < dim; y += 1)
                if (!((dummyAnswer[x][y] && grid[x][y] !== '') ||
                    (!dummyAnswer[x][y] && grid[x][y] === ''))
                ) {
                    this._displayError(`Coord° (${x}, ${y}) invalid`)
                    return false
                }

        }

        if (getFromId('titleInput').value === '') {
            this._displayError('Titre vide')
            return false
        }

        return true
    }
}