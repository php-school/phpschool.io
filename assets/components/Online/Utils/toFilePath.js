export default function (node) {
    const pathParts = []
    do {
        pathParts.push(node.name)

        node = node.parent
    } while (node !== null)

    return pathParts.reverse().join('/')
}
