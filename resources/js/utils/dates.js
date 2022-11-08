export const timeAgo = (dateParam) => {
  if (!dateParam) {
    return null
  }

  const date = typeof dateParam === 'object' ? dateParam : new Date(dateParam)
  const DAY_IN_MS = 86400000 // 24 * 60 * 60 * 1000
  const today = new Date()
  const yesterday = new Date(today - DAY_IN_MS)
  const seconds = Math.round((today - date) / 1000)
  const minutes = Math.round(seconds / 60)
  const isToday = today.toDateString() === date.toDateString()
  const isYesterday = yesterday.toDateString() === date.toDateString()
  const isThisYear = today.getFullYear() === date.getFullYear()

  if (seconds < 5) {
    return 'now'
  } else if (seconds < 60) {
    return `${ seconds } seconds ago`
  } else if (seconds < 90) {
    return 'about a minute ago'
  } else if (minutes < 60) {
    return `${ minutes } minutes ago`
  } else if (isToday) {
    return formatDate(date, 'Today') // Today at 10:20
  } else if (isYesterday) {
    return formatDate(date, 'Yesterday') // Yesterday at 10:20
  } else if (isThisYear) {
    return formatDate(date, false, true) // 10. January at 10:20
  }

  return formatDate(date) // 10. January 2017. at 10:20
}

function formatDate(date, prefomattedDate = false, hideYear = false) {

  const day = date.getDate(),
        month = 'January|February|March|April|May|June|July|August|September|October|November|December'.split('|')[date.getMonth()],
        year = date.getFullYear()

  let hours = date.getHours(),
      minutes = date.getMinutes()

  if (hours > 12) {
    // Converting from 24-hour clock
    hours = hours - 12
  }

  if (minutes < 10) {
    // Adding leading zero to minutes
    minutes = `0${ minutes }`
  }

  if (prefomattedDate) {
    // Today at 10:20
    // Yesterday at 10:20
    return `${ prefomattedDate } at ${ hours }:${ minutes }`
  }

  if (hideYear) {
    // 10. January at 10:20
    return `${ day }. ${ month } at ${ hours }:${ minutes }`
  }

  // 10. January 2017. at 10:20
  return `${ day }. ${ month } ${ year }. at ${ hours }:${ minutes }`
}