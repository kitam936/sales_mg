const nl2br = (str) => {
    var res = str.replace(/\r\n/g, "<br>");
    res = res.replace(/(\n|\r)/g, "<br>");
    return res;
}

const getToday = () => {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = ("0" + (today.getMonth()+1)).slice(-2);
    const dd = ("0" + (today.getDate())).slice(-2);
    return yyyy+"-"+mm+"-"+dd;
}

const get2YearsAgo = () => {
    const today = new Date();
    const yyyy = today.getFullYear() - 2;
    const mm = ("0" + (today.getMonth() + 1)).slice(-2);
    const dd = "01";
    return `${yyyy}-${mm}-${dd}`; // ← 文字列として返すことが重要
}

export {nl2br,getToday,get2YearsAgo};
