function checkPermission(user, permission) {
  if (typeof user.permissions !== 'undefined') {
    return user.permissions.indexOf(permission) != -1
  } else {
    return false
  }
}

const vld = {
  email: (str) => {
    // const pattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return pattern.test(str)
  },
  null: (str) => {
    const pattern = /\S+/
    return pattern.test(str)
  },
  num: (str) => {
    // const pattern = /^\d+$/
    const pattern = /^-?\d+\.?\d*$/
    return pattern.test(str)
  },
  cc: (str) => {
    const pattern = /^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|(222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11}|62[0-9]{14})$/
    return pattern.test(str)
  },
  visa: (str) => {
    const pattern = /^(?:4[0-9]{12}(?:[0-9]{3})?)$/
    return pattern.test(str)
  },
  master: (str) => {
    const pattern = /^(?:5[1-5][0-9]{14})$/
    return pattern.test(str)
  },
  amex: (str) => {
    const pattern = /^(?:3[47][0-9]{13})$/
    return pattern.test(str)
  },
  discover: (str) => {
    const pattern = /^(?:6(?:011|5[0-9][0-9])[0-9]{12})$/
    return pattern.test(str)
  },
  same: (str1, str2) => {
      return str1 === str2
  },
  phone(n) {
      const carriers = {
        '096': 'Viettel',
        '097': 'Viettel',
        '098': 'Viettel',
        '032': 'Viettel',
        '033': 'Viettel',
        '034': 'Viettel',
        '035': 'Viettel',
        '036': 'Viettel',
        '037': 'Viettel',
        '038': 'Viettel',
        '039': 'Viettel',
        '090': 'Mobifone',
        '093': 'Mobifone',
        '070': 'Mobifone',
        '071': 'Mobifone',
        '072': 'Mobifone',
        '076': 'Mobifone',
        '077': 'Mobifone',
        '078': 'Mobifone',
        '079': 'Mobifone',
        '091': 'Vinaphone',
        '094': 'Vinaphone',
        '081': 'Vinaphone',
        '082': 'Vinaphone',
        '083': 'Vinaphone',
        '084': 'Vinaphone',
        '085': 'Vinaphone',
        '087': 'Vinaphone',
        '089': 'Vinaphone',
        '059': 'Gmobile',
        '099': 'Gmobile',
        '092': 'Vietnamobile',
        '056': 'Vietnamobile',
        '058': 'Vietnamobile',
        '095': 'SFone'
      }    
      const reg = /^(^0[1|3|5|7|8|9])+([0-9]{7,9})$/
      if(!n){
        return ''
      }else{
        let resu = isNaN(n.toString()) ? '' : 'ok'
        resu = (reg.test(n)) ? resu : ''
        resu = n.length >= 11 ? '' : n.length <= 9 ? '' : resu
        resu = n.substr(0, 1) === '0' ? resu : ''
        const pr = n.substring(0,3)
        if (carriers.hasOwnProperty(pr)) {
          resu = resu == 'ok' ? carriers[pr] : ''
        }
        return resu
      }
  }
}
const is = {
  in: (obj, key) => obj && Array.isArray(obj) && key ? parseInt(obj.indexOf(key), 10) > -1 : false,
  obj: obj => typeof obj === 'object' && !Array.isArray(obj),
  arr: obj => Array.isArray(obj),
  has: (obj, key) => typeof obj === 'object' && !Array.isArray(obj) ? Object.hasOwnProperty.call(obj, key) : false,
  for: obj => Object.keys(obj)
}

export default {
  checkPermission,
  vld,
  is,
}