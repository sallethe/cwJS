import { Grid } from './grid.class'
import { c } from './commons'
import { ThemeManager } from './theme.class'

const gr = new Grid(10, [
    c(0, 0),
    c(5, 7),
])
gr.generate()

const theme = new ThemeManager()