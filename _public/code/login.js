const switchPwd = (tag) => {
    const pwd = document.getElementById(tag)
    const btn = document.getElementById(`${tag}b`)
    if (!pwd || !btn) return
    const isPwd = pwd.type === 'password'
    pwd.type = isPwd ? 'text' : 'password'
    btn.src = btn.src.replace(
        isPwd ? 'show' : 'hide',
        isPwd ? 'hide' : 'show'
    )
}