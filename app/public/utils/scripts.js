function addSkill() {
  const container = document.getElementById("skills-container");
  const skillEntry = document.createElement("div");
  skillEntry.classList.add("skill-entry");
  skillEntry.innerHTML = `
        <label for="skill_title">Skill Title</label>
        <input type="text" name="skill_title[]">
        
        <label for="skill_description">Skill Description</label>
        <textarea name="skill_description[]"></textarea>
        
        <label for="years_of_experience">Years of Experience</label>
        <input type="number" name="years_of_experience[]">
    `;
  container.appendChild(skillEntry);
}

function removeEmptySkills() {
  const container = document.getElementById("skills-container");
  const skillEntries = container.getElementsByClassName("skill-entry");
  for (let i = skillEntries.length - 1; i >= 0; i--) {
    const skillEntry = skillEntries[i];
    const title = skillEntry
      .querySelector('input[name="skill_title[]"]')
      .value.trim();
    const description = skillEntry
      .querySelector('textarea[name="skill_description[]"]')
      .value.trim();
    const yearsOfExperience = skillEntry
      .querySelector('input[name="years_of_experience[]"]')
      .value.trim();

    if (title === "" && description === "" && yearsOfExperience === "") {
      container.removeChild(skillEntry);
    }
  }
}

function addEducation() {
  const container = document.getElementById("education-container");
  const educationEntry = document.createElement("div");
  educationEntry.classList.add("education-entry");
  educationEntry.innerHTML = `
        <label for="education_institution">School</label>
        <input type="text" name="education_institution[]">
        
        <label for="education_degree">Start date</label>
        <input type="date" name="education_degree[]">
        
        <label for="education_years">End date</label>
        <input type="date" name="education_years[]">
    `;
  container.appendChild(educationEntry);
}

function removeEmptyEducation() {
  const container = document.getElementById("education-container");
  const educationEntries = container.getElementsByClassName("education-entry");
  for (let i = educationEntries.length - 1; i >= 0; i--) {
    const educationEntry = educationEntries[i];
    const institution = educationEntry
      .querySelector('input[name="education_institution[]"]')
      .value.trim();
    const degree = educationEntry
      .querySelector('input[name="education_degree[]"]')
      .value.trim();
    const years = educationEntry
      .querySelector('input[name="education_years[]"]')
      .value.trim();

    if (institution === "" && degree === "" && years === "") {
      container.removeChild(educationEntry);
    }
  }
}

function addTechnology() {
  const container = document.getElementById("technologies-container");
  const techEntry = document.createElement("div");
  techEntry.classList.add("tech-entry");
  techEntry.innerHTML = `
        <label for="tech_name">Technology Name</label>
        <input type="text" name="tech_name[]">
        
        <label for="tech_description">Technology Description</label>
        <textarea name="tech_description[]"></textarea>
    `;
  container.appendChild(techEntry);
}

function removeEmptyTechnologies() {
  const container = document.getElementById("technologies-container");
  const techEntries = container.getElementsByClassName("tech-entry");
  for (let i = techEntries.length - 1; i >= 0; i--) {
    const techEntry = techEntries[i];
    const name = techEntry
      .querySelector('input[name="tech_name[]"]')
      .value.trim();
    const description = techEntry
      .querySelector('textarea[name="tech_description[]"]')
      .value.trim();

    if (name === "" && description === "") {
      container.removeChild(techEntry);
    }
  }
}

function showProjectForm() {
  document.getElementById("project-form").style.display = "block";
}
