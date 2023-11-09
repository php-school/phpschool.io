export default function (baseName, items) {
    const pathParts = [];
    let newName = baseName;
    let i = 0;
    while (items.filter(item => item.name === newName).length) {
        i++;
        newName = baseName + ' ' + i;
    }
    return newName;
}