import toFilePath from "./toFilePath";

// The editor keeps a student's files in two shapes.
//
// Flat, keyed by path. Run and Verify send this, and it is what is saved in localStorage:
//   { "solution.php": "<?php echo 1;", "src/Util/Str.php": "<?php ..." }
//
// A tree, which the file browser and the editor tabs use. Every node links to its parent folder (null at the top):
//   [
//     { name: "solution.php", content: "<?php echo 1;", parent: null },
//     { name: "src", parent: null, children: [
//       { name: "Util", parent: <src>, children: [
//         { name: "Str.php", content: "<?php ...", parent: <Util> },
//       ] },
//     ] },
//   ]

// Flat → tree. Folders are created from the paths, in the order the paths come.
export function buildTree(files) {
  const root = [];

  for (const [path, content] of Object.entries(files)) {
    const parts = path.split("/");
    const name = parts.pop();
    let parent = null;
    let siblings = root;

    for (const dir of parts) {
      let folder = siblings.find((node) => node.name === dir && node.children);
      if (!folder) {
        folder = { name: dir, children: [], parent };
        siblings.push(folder);
      }
      parent = folder;
      siblings = folder.children;
    }

    siblings.push({ name, content, parent });
  }

  return root;
}

// Tree → flat. Files without content yet are sent as empty.
export function flatten(nodes, files = {}) {
  for (const node of nodes) {
    if (node.children) {
      flatten(node.children, files);
    } else {
      files[toFilePath(node)] = node.content ?? "";
    }
  }

  return files;
}

// The student's saved files for one exercise, kept flat as JSON under one localStorage key:
//   "learnyouphp.Hello-World" → '{"solution.php":"<?php echo 1;","src/Util/Str.php":"<?php ..."}'
export function savedFiles(workshop, exercise) {
  const key = `${workshop}.${exercise}`;

  return {
    // → flat, or {} when nothing is saved yet
    load: () => JSON.parse(localStorage.getItem(key) ?? "{}"),
    // Saves the whole tree, so deleted and renamed files are gone on reload too.
    save: (tree) => localStorage.setItem(key, JSON.stringify(flatten(tree))),
  };
}
