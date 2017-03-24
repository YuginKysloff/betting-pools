// import fs from 'fs';
import path from 'path';



const styleFolder = 'sass/page/';
const viewFolder = '../pages/';


// Копируем шаблона в папку pages
function makeView(blockName) {
	return new Promise((resolve, reject) => {
		const dest = fs.createWriteStream(`${viewFolder}view_${blockName}.php`);
		const source = fs.createReadStream(`${__dirname}/template_view.php`);
		source.pipe(dest);
		resolve();
	});
}

// Создаем папку с sass файлом для шаблона
function makeStyles(blockName) {
	return new Promise((resolve, reject) => {
		const styleBlockFolder = path.join(styleFolder, blockName);
		fs.mkdir(styleBlockFolder);
		fs.writeFile(`${styleBlockFolder}/${blockName}.scss`, `.${blockName} {}`);
		resolve();
	});
}

// Проверяем существуют ли уже подобные блоки
function viewExist(blockName) {
	return new Promise((resolve, reject) => {
		fs.access(styleFolder + blockName, err => {
			if(err) {
				resolve();
			}
			else {
				console.log(`Папка со стилями для шаблона ${blockName} уже существует!`)
				reject();
			}
		});
		fs.access(`${viewFolder}view_${blockName}.php`, err => {
			if(err) {
				resolve();
			}
			else {
				console.log(`Шаблон с именем ${blockName} уже существует!`)
				reject();
			}
		});
	});
}

// Создаем шаблон и стили к нему
function initMakeView(blockName) {
	return viewExist(blockName)
		.then(() => makeView(blockName))
		.then(() => makeStyles(blockName))
}

// Получаем данные из командной строки
const blockNameFromCli = process.argv.slice(2);

// Инициализируем создание шаблона
blockNameFromCli.forEach((block) => {
	initMakeView(block);
});