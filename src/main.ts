import { createApp, h } from 'vue';
import Home from '../resources/js/Pages/Home.vue';
import '../resources/css/app.css';

// Import initial dataset props for standalone preview / dev mode
import { projectsData } from '../resources/js/data/projectsData';
import { skillsData } from '../resources/js/data/skillsData';
import { experienceData } from '../resources/js/data/experienceData';

// Flatten or structure skills for component compatibility
const flatSkills = skillsData.flatMap((cat, catIdx) =>
  cat.skills.map((s, sIdx) => ({
    id: catIdx * 100 + sIdx + 1,
    name: s.name,
    category: catIdx === 0 ? 'frontend' : catIdx === 1 ? 'backend' : catIdx === 2 ? 'cloud' : 'ai',
    level: s.level,
    rune: s.rune,
    tag: s.categoryTag,
  }))
);

const defaultProps = {
  title: 'Code at midnight — Ma Cà Tưng Portfolio',
  projects: projectsData,
  skills: flatSkills,
  experiences: experienceData,
  stats: {
    total_pageviews: 12840,
    unique_visitors: 4920,
    total_inquiries: 18,
    total_hops: 128,
    total_projects: 6,
  },
  settings: {
    site_slogan: 'Code at midnight',
    developer_bio: 'Lead Systems Architect & Creative Full-Stack Engineer crafting supernatural web apps under the midnight moon.',
    contact_email: 'dev@macatung.dev',
    telegram_handle: '@macatung',
    github_url: 'https://github.com/macatung',
  },
};

const app = createApp({
  render: () => h(Home, defaultProps),
});

app.mount('#app');
