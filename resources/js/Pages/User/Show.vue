<template>
    <div>
        <h1>Welcome, {{ facilitator.fname }} {{ facilitator.lname }}</h1>
        <p>Email: {{ facilitator.email }}</p>
        <p>Program: {{ facilitator.program }}</p>
    </div>
</template>

<script>
export default {
    props: {
        facilitator: Object,
    },
};
</script>
