// Slider (categories on homepage)

let translate = 0;

function prevButton() {
    const slider = document.querySelector('.slider-block');
    if (translate !== 0) {
        translate += 340;
        slider.style.transform = `translate(${translate}px)`;
    }
}

function nextButton() {
    const slider = document.querySelector('.slider-block');
    const cards = document.querySelectorAll('.slider-card').length;
    let stop;

    if (window.matchMedia('(max-width: 810px)').matches) {
        stop = -cards * 340 + 340;
    } else if (window.matchMedia('(max-width: 1210px)').matches) {
        stop = -cards * 340 + 680;
    } else {
        stop = -cards * 340 + 1020;
    }

    if (translate > stop) {
        translate -= 340;
        slider.style.transform = `translate(${translate}px)`;
    } else {
        translate = stop;
        slider.style.transform = `translate(${translate}px)`;
    }
}


// Burger menu

function burgerMenu() {
    const burger = document.querySelector('.burger');
    const burgerMenuBtn = document.querySelectorAll('.burger-menu');

    if (!burger) return;

    burger.classList.toggle('burger-none');
    setTimeout(() => {
        const firstChild = burger.children[0];
        if (firstChild) firstChild.classList.toggle('burger-none');
    }, 250);

    if (burgerMenuBtn[0]) burgerMenuBtn[0].classList.toggle('none');
    if (burgerMenuBtn[1]) {
        setTimeout(() => burgerMenuBtn[1].classList.toggle('burger-active'), 30);
    }
}

// Close burger on resize
window.addEventListener('resize', () => {
    const burger = document.querySelector('.burger');
    const burgerMenuBtn = document.querySelector('.burger-menu');

    if (burger && window.matchMedia('(min-width: 320px)').matches) {
        burger.classList.add('burger-none');
    }
    if (burgerMenuBtn && window.matchMedia('(max-width: 810px)').matches) {
        burgerMenuBtn.classList.remove('none');
    }
});


// FAQ

function faq(el) {
    el.classList.toggle('faq-block-active');
    const img = el.querySelector('.faq-card img');
    if (img) img.classList.toggle('img-rotate');
}


// Category filter toggle

function categoriesShow() {
    const block = document.querySelector('.categories-filter-block');
    if (block) block.classList.toggle('categories-filter-block-active');
}


// Auth / Reg modals and user action functions
function confirmAccountDelete() {
    if (confirm('ВНИМАНИЕ! Это действие невозможно отменить. Все ваши изображения, альбомы и данные будут удалены. Вы уверены, что хотите удалить аккаунт?')) {
        if (confirm('Последнее предупреждение: Аккаунт будет удален безвозвратно. Продолжить?')) {
            document.getElementById('delete-account-form').submit();
        }
    }
}

function showCancelSubscriptionModal() {
    document.getElementById('cancelSubscriptionModal').classList.remove('none');
}

function closeCancelSubscriptionModal() {
    document.getElementById('cancelSubscriptionModal').classList.add('none');
}

function del() {
    const block = document.querySelector('.del-block');
    if (block) block.classList.toggle('none');
}

function userModal() {
    const block = document.querySelector('.user-modal-block');
    if (block) block.classList.toggle('none');
}

function auth() {
    const authBlock = document.querySelector('.auth-block');
    const regBlock = document.querySelector('.reg-block');

    if (authBlock) {
        if (regBlock && !regBlock.classList.contains('auth-reg-none')) {
            regBlock.classList.add('auth-reg-none');
        }
        authBlock.classList.remove('auth-reg-none');
        setTimeout(() => {
            const loginError = document.querySelector('#login-error');
            const passwordError = document.querySelector('#password-error');
            if (loginError) loginError.classList.add('none');
            if (passwordError) passwordError.classList.add('none');
        }, 100);
    }
}

function reg() {
    const regBlock = document.querySelector('.reg-block');
    const authBlock = document.querySelector('.auth-block');

    if (regBlock) {
        if (authBlock && !authBlock.classList.contains('auth-reg-none')) {
            authBlock.classList.add('auth-reg-none');
        }
        regBlock.classList.remove('auth-reg-none');
        setTimeout(() => {
            const regLoginError = document.querySelector('#reg-login-error');
            const regEmailError = document.querySelector('#reg-email-error');
            const regPasswordError = document.querySelector('#reg-password-error');
            const regPasswordLengthError = document.querySelector('#reg-password-length-error');
            const passwordRepeatError = document.querySelector('#password-reapeat-error');
            if (regLoginError) regLoginError.classList.add('none');
            if (regEmailError) regEmailError.classList.add('none');
            if (regPasswordError) regPasswordError.classList.add('none');
            if (regPasswordLengthError) regPasswordLengthError.classList.add('none');
            if (passwordRepeatError) passwordRepeatError.classList.add('none');
        }, 100);
    }
}

function closeAuthModal() {
    const authBlock = document.querySelector('.auth-block');
    if (authBlock) {
        authBlock.classList.add('auth-reg-none');
    }
}

function closeRegModal() {
    const regBlock = document.querySelector('.reg-block');
    if (regBlock) {
        regBlock.classList.add('auth-reg-none');
    }
}

// Registration form submit
function submitRegisterForm() {
    const form = document.getElementById('registerForm');

    clearRegisterErrors();

    if (formRegVerifyForSubmit()) {
        const submitBtn = document.getElementById('reg-button-form-submit');
        submitBtn.disabled = true;
        submitBtn.value = 'Отправка...';
        form.submit();
    }
}

function clearRegisterErrors() {
    const errorElements = document.querySelectorAll('#registerForm .input-error-text');
    errorElements.forEach(el => {
        el.classList.add('none');
        el.style.display = 'none';
    });
}

function formRegVerifyForSubmit() {
    var flagReg = true;

    const registrationLogin = document.querySelector('#regLogin');
    if (registrationLogin != null) {
        const error = document.querySelector('#reg-login-error');

        if (registrationLogin.value.trim() == '') {
            error.textContent = 'Поле логин пусто';
            error.classList.remove('none');
            flagReg = false;
        } else {
            error.classList.add('none');
        }
    }

    const registrationEmail = document.querySelector('#regEmail');
    if (registrationEmail != null) {
        const error = document.querySelector('#reg-email-error');
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (registrationEmail.value.trim() == '') {
            error.textContent = 'Поле email пусто';
            error.classList.remove('none');
            flagReg = false;
        } else if (!emailPattern.test(registrationEmail.value.trim())) {
            error.textContent = 'Введите корректную почту';
            error.classList.remove('none');
            flagReg = false;
        } else {
            error.classList.add('none');
        }
    }

    const registrationPassword = document.querySelector('#regPassword');
    if (registrationPassword != null) {
        const error = document.querySelector('#reg-password-error');
        const errorLength = document.querySelector('#reg-password-length-error');

        if (registrationPassword.value.trim() == '') {
            error.textContent = 'Поле пароль пусто';
            error.classList.remove('none');
            flagReg = false;
        } else {
            error.classList.add('none');

            if (registrationPassword.value.trim().length < 8) {
                errorLength.textContent = 'Пароль должен иметь больше 8 символов';
                errorLength.classList.remove('none');
                flagReg = false;
            } else {
                errorLength.classList.add('none');
            }
        }
    }

    if (flagReg && registrationPassword != null) {
        const registrationPasswordRepeat = document.querySelector('#passwordRepeat');
        if (registrationPasswordRepeat != null) {
            const error = document.querySelector('#password-reapeat-error');

            if (registrationPassword.value.trim() != registrationPasswordRepeat.value.trim()) {
                error.textContent = 'Пароли не совпадают';
                error.classList.remove('none');
                flagReg = false;
            } else {
                error.classList.add('none');
            }
        }
    }

    return flagReg;
}

// Image preview utilities

function imgFilter(filename) {
    if (!filename) return false;
    const ext = filename.split('.').pop().toUpperCase();
    return ['JPG', 'PNG', 'JPEG', 'SVG', 'WEBP'].includes(ext);
}

function userImgSwap() {
    const file = document.querySelector('#userImgUpdate');
    const title = document.querySelector('.user-modal-photo-chose');
    const img = document.querySelector('.user-img-modal');

    if (file && file.files[0] && imgFilter(file.files[0].name)) {
        title.textContent = file.files[0].name;
        img.src = URL.createObjectURL(file.files[0]);
    }
}


// Add image (file / url)

function addImgF() {
    const file = document.querySelector('#add-img-file');
    const block = document.querySelector('.add-img-file-path-subblock');
    const container = document.querySelector('.add-img-file-path-block');
    const urlInput = document.querySelector('#add-img-URL');
    const orText = document.querySelector('.add-img-text');
    const error = document.querySelector('#img-error');

    if (file && file.files.length) {
        if (imgFilter(file.files[0].name)) {
            container.style.background = `url(${URL.createObjectURL(file.files[0])}) no-repeat center/contain`;
            if (block) block.style.display = 'none';
            if (orText) orText.style.display = 'none';
            if (urlInput) urlInput.parentElement.style.display = 'none';
            if (error) error.classList.add('none');
        } else {
            container.style.background = '#D9D9D9';
            if (block) block.style.display = 'flex';
            if (orText) orText.style.display = 'flex';
            if (urlInput) urlInput.parentElement.style.display = 'flex';
            if (error) error.classList.remove('none');
        }
    } else {
        container.style.background = '#D9D9D9';
        if (block) block.style.display = 'flex';
        if (orText) orText.style.display = 'flex';
        if (urlInput) urlInput.parentElement.style.display = 'flex';
    }
    formVerify();
}

function addImgU() {
    const urlInput = document.querySelector('#add-img-URL');
    const file = document.querySelector('#add-img-file');
    const block = document.querySelector('.add-img-file-path-subblock');
    const container = document.querySelector('.add-img-file-path-block');
    const error = document.querySelector('#img-error');

    container.style.background = '#D9D9D9';
    if (block) block.style.display = 'flex';
    if (file) file.style.display = 'flex';

    const isValidUrl = (str) => !!new URL(str).toString();

    if (urlInput && urlInput.value.trim()) {
        if (isValidUrl(urlInput.value)) {
            const img = new Image();
            img.crossOrigin = 'Anonymous';
            img.src = urlInput.value;
            img.onload = () => {
                container.style.background = `url(${urlInput.value}) no-repeat center/contain`;
                if (block) block.style.display = 'none';
                if (file) file.style.display = 'none';
                if (error) error.classList.add('none');
                formVerify();
            };
            img.onerror = () => {
                if (error) error.classList.remove('none');
                formVerify();
            };
        } else {
            if (error) error.classList.remove('none');
            formVerify();
        }
    } else {
        if (error) error.classList.add('none');
        formVerify();
    }
}


// Category admin (file / url)

function imgCategoryFilter(filename) {
    return imgFilter(filename);
}

function addImgCategoryF() {
    const file = document.querySelector('#add-img-file-category');
    const block = document.querySelector('.add-img-file-path-subblock');
    const container = document.querySelector('.add-img-file-path-block');
    const urlInput = document.querySelector('#add-img-URL-category');
    const orText = document.querySelector('.add-img-text');
    const error = document.querySelector('#img-error-category');

    if (file && file.files.length) {
        if (imgCategoryFilter(file.files[0].name)) {
            container.style.background = `url(${URL.createObjectURL(file.files[0])}) no-repeat center/contain`;
            if (block) block.style.display = 'none';
            if (orText) orText.style.display = 'none';
            if (urlInput) urlInput.parentElement.style.display = 'none';
            if (error) error.classList.add('none');
        } else {
            container.style.background = '#D9D9D9';
            if (block) block.style.display = 'flex';
            if (orText) orText.style.display = 'flex';
            if (urlInput) urlInput.parentElement.style.display = 'flex';
            if (error) error.classList.remove('none');
        }
    } else {
        container.style.background = '#D9D9D9';
        if (block) block.style.display = 'flex';
        if (orText) orText.style.display = 'flex';
        if (urlInput) urlInput.parentElement.style.display = 'flex';
    }
    formVerifyCategory();
}

function addImgCategoryU() {
    const urlInput = document.querySelector('#add-img-URL-category');
    const file = document.querySelector('#add-img-file-category');
    const block = document.querySelector('.add-img-file-path-subblock');
    const container = document.querySelector('.add-img-file-path-block');
    const error = document.querySelector('#img-error-category');

    container.style.background = '#D9D9D9';
    if (block) block.style.display = 'flex';
    if (file) file.style.display = 'flex';

    const isValidUrl = (str) => !!new URL(str).toString();

    if (urlInput && urlInput.value.trim()) {
        if (isValidUrl(urlInput.value)) {
            const img = new Image();
            img.crossOrigin = 'Anonymous';
            img.src = urlInput.value;
            img.onload = () => {
                container.style.background = `url(${urlInput.value}) no-repeat center/contain`;
                if (block) block.style.display = 'none';
                if (file) file.style.display = 'none';
                if (error) error.classList.add('none');
                formVerifyCategory();
            };
            img.onerror = () => {
                if (error) error.classList.remove('none');
                formVerifyCategory();
            };
        } else {
            if (error) error.classList.remove('none');
            formVerifyCategory();
        }
    } else {
        if (error) error.classList.add('none');
        formVerifyCategory();
    }
}


// Form validations

function formSubmit(el) {
    let form = el;
    while (form && form.tagName !== 'FORM') form = form.parentNode;
    if (form) form.submit();
}

function formVerify() {
    let flag = true;
    let hasImage = false;

    const file = document.querySelector('#add-img-file');
    const urlInput = document.querySelector('#add-img-URL');
    const error = document.querySelector('#img-error');

    const isCreatePage = file !== null || urlInput !== null;

    if (isCreatePage) {
        if (file && file.files.length) {
            if (imgFilter(file.files[0].name)) {
                hasImage = true;
                if (error) error.classList.add('none');
            } else {
                if (error) error.classList.remove('none');
                flag = false;
            }
        } else if (urlInput && urlInput.value.trim()) {
            const isValidUrl = (str) => !!new URL(str).toString();
            if (isValidUrl(urlInput.value)) {
                hasImage = true;
                if (error) error.classList.add('none');
            } else {
                if (error) error.classList.remove('none');
                flag = false;
            }
        } else {
            flag = false;
        }
    } else {
        hasImage = true;
    }

    const nameInput = document.querySelector('#add-img-name');
    if (nameInput) {
        const nameError = document.querySelector('#name-error');
        if (!nameInput.value.trim()) {
            if (nameError) nameError.classList.remove('none');
            flag = false;
        } else {
            if (nameError) nameError.classList.add('none');
        }
    }

    const categoryRadios = document.querySelectorAll('input[name="category_id"]');
    if (categoryRadios.length) {
        let categorySelected = false;
        categoryRadios.forEach(r => { if (r.checked) categorySelected = true; });
        const catError = document.querySelector('#categories-error');
        if (!categorySelected) {
            if (catError) catError.classList.remove('none');
            flag = false;
        } else {
            if (catError) catError.classList.add('none');
        }
    }

    const tags = document.querySelectorAll('input[name="tags[]"]:checked');
    if (tags.length > 8) {
        tags[tags.length - 1].checked = false;
        alert('Можно выбрать не более 8 тегов');
    }

    const btn = document.querySelector('#button-form-submit');
    if (flag && hasImage && btn) {
        btn.disabled = false;
        btn.classList.remove('unactive-button');
    } else if (btn) {
        btn.disabled = true;
        btn.classList.add('unactive-button');
    }
}

function formVerifyCategory() {
    let flag = true;
    let hasImage = false;

    const file = document.querySelector('#add-img-file-category');
    const urlInput = document.querySelector('#add-img-URL-category');
    const error = document.querySelector('#img-error-category');

    if (file && file.files.length) {
        if (imgCategoryFilter(file.files[0].name)) {
            hasImage = true;
            if (error) error.classList.add('none');
        } else {
            if (error) error.classList.remove('none');
            flag = false;
        }
    } else if (urlInput && urlInput.value.trim()) {
        const isValidUrl = (str) => !!new URL(str).toString();
        if (isValidUrl(urlInput.value)) {
            hasImage = true;
            if (error) error.classList.add('none');
        } else {
            if (error) error.classList.remove('none');
            flag = false;
        }
    } else {
        flag = false;
    }

    const nameInput = document.querySelector('#add-img-name-category');
    if (nameInput) {
        const nameError = document.querySelector('#name-error-category');
        if (!nameInput.value.trim()) {
            if (nameError) nameError.classList.remove('none');
            flag = false;
        } else {
            if (nameError) nameError.classList.add('none');
        }
    }

    const btn = document.querySelector('#button-form-submit-category');
    if (flag && hasImage && btn) {
        btn.disabled = false;
        btn.classList.remove('unactive-button');
    } else if (btn) {
        btn.disabled = true;
        btn.classList.add('unactive-button');
    }
}

function formUpdateVerify() {
    const nameInput = document.querySelector('#update-name');
    const error = document.querySelector('#name-update-error');
    const btn = document.querySelector('#button-form-update-submit');

    if (!nameInput) return;

    let valid = true;
    if (!nameInput.value.trim()) {
        if (error) error.classList.remove('none');
        valid = false;
    } else {
        if (error) error.classList.add('none');
    }

    if (btn) {
        if (valid) {
            btn.disabled = false;
            btn.classList.remove('unactive-button');
        } else {
            btn.disabled = true;
            btn.classList.add('unactive-button');
        }
    }
}

function formAuthVerify() {
    let valid = true;
    const login = document.querySelector('#authLogin');
    const pass = document.querySelector('#authPassword');
    const loginError = document.querySelector('#login-error');
    const passError = document.querySelector('#password-error');

    if (login && loginError) {
        if (!login.value.trim()) {
            loginError.textContent = 'Поле логин пусто';
            loginError.classList.remove('none');
            valid = false;
        } else {
            loginError.classList.add('none');
        }
    }

    if (pass && passError) {
        if (!pass.value.trim()) {
            passError.textContent = 'Поле пароль пусто';
            passError.classList.remove('none');
            valid = false;
        } else {
            passError.classList.add('none');
        }
    }

    const btn = document.querySelector('#button-form-submit');
    if (btn) {
        if (valid) {
            btn.disabled = false;
            btn.classList.remove('unactive-button');
        } else {
            btn.disabled = true;
            btn.classList.add('unactive-button');
        }
    }
}

function formRegVerify() {
    let valid = true;

    const login = document.querySelector('#regLogin');
    const email = document.querySelector('#regEmail');
    const pass = document.querySelector('#regPassword');
    const passRepeat = document.querySelector('#passwordRepeat');

    const loginError = document.querySelector('#reg-login-error');
    const emailError = document.querySelector('#reg-email-error');
    const passError = document.querySelector('#reg-password-error');
    const passLengthError = document.querySelector('#reg-password-length-error');
    const repeatError = document.querySelector('#password-reapeat-error');

    if (login && loginError) {
        if (!login.value.trim()) {
            loginError.textContent = 'Поле логин пусто';
            loginError.classList.remove('none');
            valid = false;
        } else {
            loginError.classList.add('none');
        }
    }

    if (email && emailError) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim()) {
            emailError.textContent = 'Поле email пусто';
            emailError.classList.remove('none');
            valid = false;
        } else if (!emailPattern.test(email.value.trim())) {
            emailError.textContent = 'Введите корректную почту';
            emailError.classList.remove('none');
            valid = false;
        } else {
            emailError.classList.add('none');
        }
    }

    if (pass && passError && passLengthError) {
        if (!pass.value.trim()) {
            passError.textContent = 'Поле пароль пусто';
            passError.classList.remove('none');
            valid = false;
        } else {
            passError.classList.add('none');
            if (pass.value.trim().length < 8) {
                passLengthError.textContent = 'Пароль должен иметь больше 8 символов';
                passLengthError.classList.remove('none');
                valid = false;
            } else {
                passLengthError.classList.add('none');
            }
        }
    }

    if (valid && pass && passRepeat && repeatError) {
        if (pass.value.trim() !== passRepeat.value.trim()) {
            repeatError.textContent = 'Пароли не совпадают';
            repeatError.classList.remove('none');
            valid = false;
        } else {
            repeatError.classList.add('none');
        }
    }

    const btn = document.querySelector('#reg-button-form-submit');
    if (btn) {
        if (valid) {
            btn.disabled = false;
            btn.classList.remove('unactive-button');
        } else {
            btn.disabled = true;
            btn.classList.add('unactive-button');
        }
    }
}


// Dropdowns for tags / categories

function viewCategoriesTag(el) {
    const dropdown = el?.children[1];
    const arrow = el?.querySelector('.input-select-arrow');
    if (dropdown) dropdown.classList.toggle('input-datalist-active');
    if (arrow) arrow.classList.toggle('input-select-arrow-active');
}

function viewCategoriesTagRemove(el) {
    const dropdown = el?.children[1];
    const arrow = el?.querySelector('.input-select-arrow');
    if (dropdown) dropdown.classList.remove('input-datalist-active');
    if (arrow) arrow.classList.remove('input-select-arrow-active');
}

function tagCategoriesUpdate() {
    const block = document.querySelector('.update-block');
    if (block) block.classList.toggle('none');
}


// Likes & favorites (AJAX)

function likeSubmit(element, event) {
    event.preventDefault();

    const userIdInput = element.querySelector('input[name="likeUserId"]') ||
        element.querySelector('input[name="bookmarkUserId"]');
    const userId = userIdInput?.value;

    if (!userId || userId == -1) {
        auth();
        return false;
    }

    const url = element.getAttribute('data-url');
    if (!url) return;

    const numbers = element.querySelector('.like-numbers');
    const icon = element.querySelector('.gallery-like');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    })
        .then(res => res.json())
        .then(data => {
            if (numbers) numbers.textContent = data.count;
            if (data.liked) {
                if (numbers) numbers.classList.add('like-numbers-active');
                if (icon) icon.classList.add('gallery-like-active');
            } else {
                if (numbers) numbers.classList.remove('like-numbers-active');
                if (icon) icon.classList.remove('gallery-like-active');
            }
        })
        .catch(err => console.warn('Like error:', err));
}

function bookmarkSubmit(element, event) {
    event.preventDefault();

    const userIdInput = element.querySelector('input[name="bookmarkUserId"]');
    const userId = userIdInput?.value;

    if (!userId || userId == -1) {
        auth();
        return false;
    }

    const url = element.getAttribute('data-url');
    if (!url) return;

    const icon = element.querySelector('.gallery-bookmark');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    })
        .then(res => res.json())
        .then(data => {
            if (data.favorited) {
                if (icon) icon.classList.add('gallery-bookmark-active');
            } else {
                if (icon) icon.classList.remove('gallery-bookmark-active');
            }
        })
        .catch(err => console.warn('Bookmark error:', err));
}

function bookmarkSubmitUserPage(element, event) {
    event.preventDefault();

    const userIdInput = element.querySelector('input[name="bookmarkUserId"]');
    const userId = userIdInput?.value;

    if (!userId || userId == -1) {
        auth();
        return false;
    }

    const url = element.getAttribute('data-url');
    if (!url) return;

    const icon = element.querySelector('.gallery-bookmark');
    const imgBlock = element.closest('.img-block');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    })
        .then(res => res.json())
        .then(data => {
            if (!data.favorited && imgBlock) {
                imgBlock.remove();
                const gallery = document.querySelector('.gallery_block');
                if (gallery && gallery.children.length === 0) {
                    const container = document.querySelector('.gallery');
                    if (container) container.innerHTML = '<div class="no-img-block flex-center"><h2>Изображения не найдены</h2></div>';
                }
            } else if (icon) {
                if (data.favorited) icon.classList.add('gallery-bookmark-active');
                else icon.classList.remove('gallery-bookmark-active');
            }
        })
        .catch(err => console.warn('Bookmark error:', err));
}

function deleteBookmarkInFavorites(el) {
    const imgBlock = el.closest('.img-block');
    imgBlock.remove();

    const galleryContainer = document.querySelector('#galleryId');

    if (galleryContainer.children.length === 0) {
        galleryContainer.innerHTML = '';
        const noImageBlock = document.createElement('div');
        noImageBlock.className = 'no-img-block flex-center';
        noImageBlock.innerHTML = '<h2>Изображения не найдены</h2>';
        galleryContainer.appendChild(noImageBlock);
    }
}


// Admin helpers

function setDeleteUrl(url) {
    const form = document.getElementById('deleteForm');
    if (form) form.action = url;
}


// General UI helpers

function showMessage(text) {
    const block = document.querySelector('.message-block');
    const title = document.querySelector('.message-title');
    if (title && text) title.textContent = text;
    if (block) block.classList.toggle('none');
}

// Restore scroll position on page load
document.addEventListener('DOMContentLoaded', () => {
    const all = document.querySelector('*');
    if (all) {
        all.style.scrollBehavior = 'auto';
        const saved = localStorage.getItem('scrollPage');
        if (saved) window.scrollTo(0, parseInt(saved));
        all.style.scrollBehavior = 'smooth';
    }
    localStorage.setItem('scrollPage', 0);

    if (document.querySelector('#add-img-file-category')) formVerifyCategory();
    if (document.querySelector('#update-name')) formUpdateVerify();
});