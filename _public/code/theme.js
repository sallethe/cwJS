const THEME_LOCAL_STORAGE_ID = 'cwjs-theme'
const THEME_DATASET_ID = 'theme'

let ThemeTag = {
    'DARK': 'dark',
    'LIGHT': 'light',
}

class ThemeManager {
    constructor() {
        this.setTheme(this.getTheme())
        const e = document.getElementById('theme')
        if (!e)
            throw new Error('the theming button doesn\'t exist.')
        e.onclick = () => this.switchTheme()
    }

    switchTheme() {
        const theme = this.getTheme()
        this.setTheme(theme === ThemeTag.DARK ?
            ThemeTag.LIGHT
            : ThemeTag.DARK)
    }

    getTheme() {
        const theme = localStorage.getItem(THEME_LOCAL_STORAGE_ID)
        return theme === ThemeTag.DARK ?
            ThemeTag.DARK
            : ThemeTag.LIGHT
    }

    setTheme(theme) {
        document.documentElement.dataset[THEME_DATASET_ID] = theme
        localStorage.setItem(THEME_LOCAL_STORAGE_ID, theme)
    }
}

new ThemeManager();