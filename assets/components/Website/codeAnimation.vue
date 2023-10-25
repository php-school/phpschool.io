<template>
  <div class="container">
    <div class="code-container">
      <div class="code-content">
        <div class="code-lines">
          <div class="code-line" v-for="(line, index) in displayedCode" :key="index">{{ line }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      numRows: 20,
      rowHeight: 20,
      animationSpeed: 400,
      rows: [],
    };
  },
  mounted() {
    this.matrixContainer = this.$el.querySelector('.code-content');
    this.createMatrix();
    this.startScrollingAnimation();
  },
  methods: {
    getRandomCodeSnippet() {
      const codeSnippets = [
        "learnToCode('PHP');",
        "<?php echo 'Hello, world!'; ?>",
        "$variable = 42;",
        "function add($a, $b) { return $a + $b; }",
        "$array = [1, 2, 3, 4, 5];",
        "class MyClass { $name $skill; }",
        "// This is a PHP comment",
        "<?php $programmingLanguages = ['PHP', 'Javascript']\;",
        "$favoriteLanguage = 'PHP'\;",
        "$developer = new Programmer('You', 'expert')\;",
      ];
      return codeSnippets[Math.floor(Math.random() * codeSnippets.length)];
    },
    createMatrix() {
      for (let i = 0; i < this.numRows; i++) {
        this.addRow();
      }
    },
    addRow() {
      const row = document.createElement('div');
      row.className = 'code-line';
      row.style.height = `${this.rowHeight}px`;
      row.textContent = this.getRandomCodeSnippet();
      this.matrixContainer.appendChild(row);
      this.rows.push(row);
    },
    startScrollingAnimation() {
      setInterval(() => {
        const firstRow = this.rows.shift();
        firstRow.textContent = this.getRandomCodeSnippet();
        this.matrixContainer.appendChild(firstRow);
        this.rows.push(firstRow);
      }, this.animationSpeed);
    },
  },
};
</script>

<style scoped>
.code-container {
  overflow: hidden;
  width: 100%;
  height: 100%;
  color: white;
  font-family: 'Orbitron', sans-serif;
}

.code-content {
  display: flex;
  flex-direction: column;
  animation: scrollAnimation 20s linear infinite;
}

.code-lines {
  position: relative;
}

.code-line {
  margin: 0;
  padding: 0;
  width: 100%;
}
</style>
