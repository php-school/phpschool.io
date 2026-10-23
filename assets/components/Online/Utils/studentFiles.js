import toFilePath from "./toFilePath";

// Builds the file tree from [path, content] pairs such as ["src/Foo.php", "<?php"],
// linking every node to its parent. A later pair for the same path replaces the content.
export function buildTree(pairs) {
  const root = [];

  for (const [path, content] of pairs) {
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

    const file = siblings.find((node) => node.name === name && !node.children);
    if (file) {
      file.content = content;
    } else {
      siblings.push({ name, content, parent });
    }
  }

  return root;
}

// Flattens the tree into { "src/Foo.php": content }.
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

// The student's files for one exercise, kept in localStorage under "<workshop>.<exercise>.<path>".
export function savedFiles(workshop, exercise) {
  const prefix = `${workshop}.${exercise}.`;
  const keys = () => Object.keys(localStorage).filter((key) => key.startsWith(prefix));
  const clear = () => keys().forEach((key) => localStorage.removeItem(key));

  return {
    load: () => keys().map((key) => [key.slice(prefix.length), localStorage.getItem(key)]),
    save: (file, content) => localStorage.setItem(prefix + toFilePath(file), content),
    clear,
    // Rewrites storage from the tree, so deleted and renamed files don't come back on reload.
    sync: (tree) => {
      clear();
      for (const [path, content] of Object.entries(flatten(tree))) {
        localStorage.setItem(prefix + path, content);
      }
    },
  };
}
